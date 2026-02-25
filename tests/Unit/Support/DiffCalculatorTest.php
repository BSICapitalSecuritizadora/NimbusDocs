<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\DiffCalculator;
use PHPUnit\Framework\TestCase;

class DiffCalculatorTest extends TestCase
{
    public function testComputeReturnsEmptyIfIdentical(): void
    {
        $data = ['foo' => 'bar', 'a' => 1];
        $diff = DiffCalculator::compute($data, $data);

        $this->assertEmpty($diff);
    }

    public function testComputeDetectsChanges(): void
    {
        $old = ['name' => 'John', 'role' => 'USER'];
        $new = ['name' => 'John', 'role' => 'ADMIN'];

        $diff = DiffCalculator::compute($old, $new);

        $this->assertCount(1, $diff);
        $this->assertArrayHasKey('role', $diff);
        $this->assertEquals('USER', $diff['role']['old']);
        $this->assertEquals('ADMIN', $diff['role']['new']);
    }

    public function testComputeDetectsAdditions(): void
    {
        $old = ['a' => 1];
        $new = ['a' => 1, 'b' => 2];

        $diff = DiffCalculator::compute($old, $new);

        $this->assertCount(1, $diff);
        $this->assertArrayHasKey('b', $diff);
        $this->assertNull($diff['b']['old']);
        $this->assertEquals(2, $diff['b']['new']);
    }

    public function testComputeDetectsRemovals(): void
    {
        $old = ['a' => 1, 'b' => 2];
        $new = ['a' => 1];

        $diff = DiffCalculator::compute($old, $new);

        $this->assertCount(1, $diff);
        $this->assertArrayHasKey('b', $diff);
        $this->assertEquals(2, $diff['b']['old']);
        $this->assertNull($diff['b']['new']);
    }

    public function testComputeHandlesMixedTypes(): void
    {
        $old = ['active' => true, 'score' => 10];
        $new = ['active' => 1, 'score' => '10']; // PHP loose comparison might trigger diff if strict check used

        // O DiffCalculator usa !== (strict)
        $diff = DiffCalculator::compute($old, $new);

        $this->assertCount(2, $diff);
        $this->assertEquals(true, $diff['active']['old']);
        $this->assertEquals(1, $diff['active']['new']);
    }
}
