<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        parent::tearDown();
    }

    public function testSetAndGet(): void
    {
        Session::set('test_key', 'test_value');

        $this->assertEquals('test_value', Session::get('test_key'));
    }

    public function testGetWithDefault(): void
    {
        $this->assertNull(Session::get('nonexistent'));
        $this->assertEquals('default', Session::get('nonexistent', 'default'));
    }

    public function testHas(): void
    {
        $this->assertFalse(Session::has('test_key'));

        Session::set('test_key', 'value');

        $this->assertTrue(Session::has('test_key'));
    }

    public function testRemove(): void
    {
        Session::set('test_key', 'value');
        $this->assertTrue(Session::has('test_key'));

        Session::remove('test_key');

        $this->assertFalse(Session::has('test_key'));
    }

    public function testFlashMessages(): void
    {
        Session::flash('success', 'Operation successful');

        $this->assertEquals('Operation successful', Session::getFlash('success'));
        $this->assertNull(Session::getFlash('success'), 'Flash message should be removed after first retrieval');
    }

    public function testFlashWithMultipleKeys(): void
    {
        Session::flash('success', 'Success message');
        Session::flash('error', 'Error message');

        $this->assertEquals('Success message', Session::getFlash('success'));
        $this->assertEquals('Error message', Session::getFlash('error'));
    }

    public function testFlashRetrievalTwice(): void
    {
        Session::flash('message', 'Test');

        $first = Session::getFlash('message');
        $second = Session::getFlash('message');

        $this->assertEquals('Test', $first);
        $this->assertNull($second, 'Flash should only be available once');
    }

    public function testSetOverwritesValue(): void
    {
        Session::set('key', 'value1');
        Session::set('key', 'value2');

        $this->assertEquals('value2', Session::get('key'));
    }

    public function testSetDifferentTypes(): void
    {
        Session::set('string', 'text');
        Session::set('int', 42);
        Session::set('bool', true);
        Session::set('array', ['a', 'b', 'c']);
        Session::set('null', null);

        $this->assertEquals('text', Session::get('string'));
        $this->assertEquals(42, Session::get('int'));
        $this->assertTrue(Session::get('bool'));
        $this->assertEquals(['a', 'b', 'c'], Session::get('array'));
        $this->assertNull(Session::get('null'));
    }

    public function testHasWithNullValue(): void
    {
        Session::set('null_key', null);

        $this->assertTrue(Session::has('null_key'), 'has() should return true even for null values');
    }

    public function testRemoveNonexistent(): void
    {
        // Should not throw exception
        Session::remove('nonexistent_key');

        $this->assertFalse(Session::has('nonexistent_key'));
    }

    public function testGetFlashWithoutSetting(): void
    {
        $this->assertNull(Session::getFlash('nonexistent'));
    }

    public function testMultipleFlashMessages(): void
    {
        Session::flash('msg1', 'Message 1');
        Session::flash('msg2', 'Message 2');
        Session::flash('msg3', 'Message 3');

        $this->assertEquals('Message 1', Session::getFlash('msg1'));
        $this->assertEquals('Message 2', Session::getFlash('msg2'));
        $this->assertEquals('Message 3', Session::getFlash('msg3'));

        // All should be gone after retrieval
        $this->assertNull(Session::getFlash('msg1'));
        $this->assertNull(Session::getFlash('msg2'));
        $this->assertNull(Session::getFlash('msg3'));
    }

    public function testSetEmptyString(): void
    {
        Session::set('empty', '');

        $this->assertTrue(Session::has('empty'));
        $this->assertEquals('', Session::get('empty'));
    }

    public function testArrayAccess(): void
    {
        Session::set('user', ['id' => 1, 'name' => 'John']);

        $user = Session::get('user');
        $this->assertEquals(1, $user['id']);
        $this->assertEquals('John', $user['name']);
    }

    public function testNestedArrays(): void
    {
        $data = [
            'level1' => [
                'level2' => [
                    'level3' => 'deep value',
                ],
            ],
        ];

        Session::set('nested', $data);

        $retrieved = Session::get('nested');
        $this->assertEquals('deep value', $retrieved['level1']['level2']['level3']);
    }
}
