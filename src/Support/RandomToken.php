<?php

declare(strict_types=1);

namespace App\Support;

final class RandomToken
{
    /**
     * Gera um código curto para o usuário final.
     * Ex.: ABCD-1234-EFGH
     */
    public static function shortCode(int $length = 12): string
    {
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789'; // sem O, I, 0, 1
        $maxIndex = strlen($alphabet) - 1;

        $chars = [];
        for ($i = 0; $i < $length; $i++) {
            $chars[] = $alphabet[random_int(0, $maxIndex)];
        }

        // opcional: agrupar em blocos de 4
        return strtoupper(implode('', array_chunk($chars, 4, true)
            ? array_map(fn ($chunk) => implode('', $chunk), array_chunk($chars, 4))
            : $chars));
    }
}
