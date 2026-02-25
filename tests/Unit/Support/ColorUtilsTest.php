<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\ColorUtils;
use PHPUnit\Framework\TestCase;

class ColorUtilsTest extends TestCase
{
    public function testAdjustBrightnessLightens(): void
    {
        // Black #000000 + 10 -> #0a0a0a
        $lighter = ColorUtils::adjustBrightness('#000000', 10);
        $this->assertEquals('#0a0a0a', $lighter);
    }

    public function testAdjustBrightnessDarkens(): void
    {
        // White #ffffff - 10 -> #f5f5f5
        // 255 - 10 = 245 = 0xf5
        $darker = ColorUtils::adjustBrightness('#ffffff', -10);
        $this->assertEquals('#f5f5f5', $darker);
    }

    public function testAdjustBrightnessClampsToLimits(): void
    {
        // Should clamp to 255 (white)
        $this->assertEquals('#ffffff', ColorUtils::adjustBrightness('#ffffff', 100));

        // Should clamp to 0 (black)
        $this->assertEquals('#000000', ColorUtils::adjustBrightness('#000000', -100));
    }

    public function testAdjustBrightnessHandlesShortHex(): void
    {
        // #fff -> #ffffff. -10 -> #f5f5f5
        $this->assertEquals('#f5f5f5', ColorUtils::adjustBrightness('#fff', -10));

        // #000 -> #000000. +10 -> #0a0a0a
        $this->assertEquals('#0a0a0a', ColorUtils::adjustBrightness('000', 10)); // sem #
    }
}
