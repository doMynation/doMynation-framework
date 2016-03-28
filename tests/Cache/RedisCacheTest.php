<?php

class RedisCacheTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Domynation\Cache\RedisCache
     */
    protected $redis;

    public function tearDown()
    {
        $this->redis->flush();
    }

    public function setUp()
    {
        $this->redis = new \Domynation\Cache\RedisCache('127.0.0.1', 6379);
        $this->redis->setPrefix('unittest');
    }


    /**
     * @test
     */
    public function it_sets_one_item()
    {
        $this->redis->set('item1', 18, 1);

        $this->assertEquals(18, $this->redis->get('item1'));
    }

    /**
     * @test
     */
    public function it_gets_a_numeric_item()
    {
        $this->redis->set('item1', 18, 1);

        $this->assertEquals(18, $this->redis->get('item1'));
    }

    /**
     * @test
     */
    public function it_gets_an_object_item()
    {
        $obj = new stdClass();
        $obj->name = "Jesus";

        $this->redis->set('item1', $obj, 10);

        $item = $this->redis->get('item1');

        $this->assertEquals($obj, $item);
    }

    /**
     * @test
     */
    public function it_checks_if_an_item_exists()
    {
        $this->assertFalse($this->redis->exists('non-existing'));

        $this->redis->set('someItem', 'someValue', 10);

        $this->assertTrue($this->redis->exists('someItem'));
    }

    /**
     * @test
     */
    public function it_increments_a_value()
    {
        $this->redis->set('counter', 1, 10);
        $this->redis->set('counterByTwo', 1, 10);

        $this->redis->increment('counter');
        $this->redis->increment('counterByTwo', 2);

        $this->assertEquals(2, $this->redis->get('counter'));
        $this->assertEquals(3, $this->redis->get('counterByTwo'));
    }

    /**
     * @test
     */
    public function it_decrements_a_value()
    {
        $this->redis->set('counter', 10, 10);
        $this->redis->set('counterByTwo', 10, 10);

        $this->redis->decrement('counter');
        $this->redis->decrement('counterByTwo', 2);

        $this->assertEquals(9, $this->redis->get('counter'));
        $this->assertEquals(8, $this->redis->get('counterByTwo'));
    }

    /**
     * @test
     */
    public function it_tests_the_prefix()
    {
        $this->redis->setPrefix("jesus");

        $this->assertEquals("jesus", $this->redis->getPrefix());
    }

    /**
     * @test
     */
    public function it_deletes_an_item()
    {
        $this->redis->set('item1', 'some value', 10);
        $this->redis->set('item2', 'some other value', 10);
        $this->redis->set('item3', 'some other value', 10);

        $this->redis->delete('item1');
        $this->redis->delete('item3');

        $this->assertFalse($this->redis->exists('item1'));
        $this->assertTrue($this->redis->exists('item2'));
        $this->assertFalse($this->redis->exists('item3'));
    }

    /**
     * @test
     */
    public function it_sets_many_items()
    {
        $this->redis->setMany([
            'item1' => 'value1',
            'item2' => 10
        ], 10);

        $this->assertTrue($this->redis->exists('item1'));
        $this->assertTrue($this->redis->exists('item2'));

        $this->assertEquals('value1', $this->redis->get('item1'));
        $this->assertEquals(10, $this->redis->get('item2'));
    }

    /**
     * @test
     */
    public function it_gets_many_items()
    {
        $this->redis->setMany([
            'item1' => 'value1',
            'item2' => 10
        ], 10);

        $this->assertEquals(['value1', 10], $this->redis->getMany(['item1', 'item2']));
    }

    /**
     * @test
     */
    public function it_pulls_an_item()
    {
        $this->redis->set('item1', 'value', 10);

        $value = $this->redis->pull('item1');

        $this->assertEquals('value', $value);
        $this->assertFalse($this->redis->exists('item1'));
    }
}
