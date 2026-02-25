<?php

declare(strict_types=1);

namespace App\Support;

final class Csrf
{
    private const KEY = '_csrf_token';
    private const TS_KEY = '_csrf_token_ts';
    private const PREV_KEY = '_csrf_token_prev';
    private const PREV_TS_KEY = '_csrf_token_prev_ts';
    private const TTL = 7200;       // 120 min — vida útil do token
    private const GRACE_TTL = 60;   // 60s — janela de graça para o token anterior

    /**
     * Retorna o token CSRF atual (gera um novo se não existir).
     */
    public static function token(): string
    {
        if (empty($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            self::regenerate();
        }

        return (string) $_SESSION[self::KEY];
    }

    /**
     * Valida o token e rotaciona automaticamente após uso bem-sucedido.
     *
     * Aceita o token atual OU o token anterior (dentro da janela de graça),
     * garantindo que múltiplas abas ou submissões quase simultâneas não quebrem.
     */
    public static function validate(?string $token): bool
    {
        if (!is_string($token) || $token === '') {
            return false;
        }

        // 1. Tenta validar contra o token ATUAL
        if (self::matchesCurrent($token)) {
            self::rotate();

            return true;
        }

        // 2. Tenta validar contra o token ANTERIOR (janela de graça)
        if (self::matchesPrevious($token)) {
            // Não rotaciona de novo — o token já foi rotacionado quando o "atual" foi usado
            return true;
        }

        return false;
    }

    /**
     * Valida o token mas NÃO o rotaciona. Ideal para chamadas AJAX intermediárias
     * onde o usuário ainda vai submeter o formulário principal depois.
     */
    public static function validateWithoutRotation(?string $token): bool
    {
        if (!is_string($token) || $token === '') {
            return false;
        }

        if (self::matchesCurrent($token)) {
            return true;
        }

        if (self::matchesPrevious($token)) {
            return true;
        }

        return false;
    }

    /**
     * Força regeneração do token (útil após login, por exemplo).
     */
    public static function regenerate(): void
    {
        $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        $_SESSION[self::TS_KEY] = time();
    }

    /**
     * Rotaciona: move o token atual para "anterior" e gera um novo.
     */
    private static function rotate(): void
    {
        // Salva o token atual como "anterior" para a janela de graça
        $_SESSION[self::PREV_KEY] = $_SESSION[self::KEY] ?? null;
        $_SESSION[self::PREV_TS_KEY] = time();

        // Gera novo token
        self::regenerate();
    }

    private static function matchesCurrent(?string $token): bool
    {
        if (empty($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            return false;
        }

        $stored = (string) $_SESSION[self::KEY];
        $ts = (int) ($_SESSION[self::TS_KEY] ?? 0);

        return hash_equals($stored, (string) $token)
            && (time() - $ts) <= self::TTL;
    }

    private static function matchesPrevious(?string $token): bool
    {
        if (empty($_SESSION[self::PREV_KEY]) || !is_string($_SESSION[self::PREV_KEY])) {
            return false;
        }

        $stored = (string) $_SESSION[self::PREV_KEY];
        $ts = (int) ($_SESSION[self::PREV_TS_KEY] ?? 0);

        // O token anterior só vale dentro da janela de graça (60s)
        return hash_equals($stored, (string) $token)
            && (time() - $ts) <= self::GRACE_TTL;
    }
}
