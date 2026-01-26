<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Helper para tradução e formatação de status de submissões.
 * Centraliza a lógica de tradução de status para evitar duplicação em views.
 */
class StatusHelper
{
    /**
     * Traduz um status raw para suas propriedades de exibição.
     *
     * @param string $status Status bruto do banco de dados
     * @return array{label: string, badge: string, icon: string}
     */
    public static function translate(string $status): array
    {
        return match ($status) {
            'PENDING' => [
                'label' => 'Pendente',
                'badge' => 'nd-badge-warning',
                'icon'  => 'bi-hourglass',
            ],
            'IN_REVIEW', 'UNDER_REVIEW' => [
                'label' => 'Em Análise',
                'badge' => 'nd-badge-info',
                'icon'  => 'bi-search',
            ],
            'APPROVED', 'COMPLETED', 'FINALIZADA' => [
                'label' => 'Concluído',
                'badge' => 'nd-badge-success',
                'icon'  => 'bi-check2-circle',
            ],
            'REJECTED', 'REJEITADA' => [
                'label' => 'Rejeitado',
                'badge' => 'nd-badge-danger',
                'icon'  => 'bi-x-circle',
            ],
            default => [
                'label' => $status,
                'badge' => 'nd-badge-secondary',
                'icon'  => 'bi-circle',
            ],
        };
    }

    /**
     * Retorna apenas o label traduzido do status.
     */
    public static function label(string $status): string
    {
        return self::translate($status)['label'];
    }

    /**
     * Retorna apenas a classe CSS do badge.
     */
    public static function badgeClass(string $status): string
    {
        return self::translate($status)['badge'];
    }

    /**
     * Retorna apenas a classe do ícone.
     */
    public static function iconClass(string $status): string
    {
        return self::translate($status)['icon'];
    }
}
