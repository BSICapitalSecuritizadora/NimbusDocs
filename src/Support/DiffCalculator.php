<?php

declare(strict_types=1);

namespace App\Support;

final class DiffCalculator
{
    /**
     * Computes the difference between two arrays (old vs new).
     * Returns an array only with changed keys.
     * 
     * Output format:
     * [
     *    'key' => [
     *        'old' => 'value1',
     *        'new' => 'value2'
     *    ]
     * ]
     */
    public static function compute(array $old, array $new): array
    {
        $diff = [];

        // Check for removed or changed items
        foreach ($old as $key => $value) {
            if (!array_key_exists($key, $new)) {
                $diff[$key] = [
                    'old' => $value,
                    'new' => null // Removed
                ];
                continue;
            }

            if ($new[$key] !== $value) {
                // If both are arrays, recurse? 
                // For simplicity in audit logs, we usually treat sub-arrays as value replacements unless complex.
                // Let's do simple comparison for now.
                $diff[$key] = [
                    'old' => $value,
                    'new' => $new[$key]
                ];
            }
        }

        // Check for added items
        foreach ($new as $key => $value) {
            if (!array_key_exists($key, $old)) {
                $diff[$key] = [
                    'old' => null, // Added
                    'new' => $value
                ];
            }
        }

        return $diff;
    }
}
