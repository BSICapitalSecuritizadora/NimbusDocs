<?php

namespace App\Support;

class ColorUtils
{
    /**
     * Adjust brightness of a hex color.
     * @param string $hex The hex color (e.g., #000000)
     * @param int $steps Steps to adjust (-255 to 255). Negative = darker, Positive = lighter.
     * @return string
     */
    public static function adjustBrightness(string $hex, int $steps): string
    {
        // Remove #
        $hex = ltrim($hex, '#');
        
        // Expand short form (e.g. "036")
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        // Convert to decimal
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Adjust
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        // Convert back to hex
        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
                   . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
                   . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
}
