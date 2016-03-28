<?php

class InMemoryCacheTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Domynation\Cache\InMemoryCache
     */
    protected $cache;

    public function tearDown()
    {
        $this->cache->flush();
    }

    public function setUp()
    {
        $this->cache = new \Domynation\Cache\InMemoryCache;
        $this->cache->setPrefix('unittest');
    }


    /**
     * @test
     */
    public function it_sets_an_item()
    {
        $this->cache->set('item1', 18, 1);

        $this->assertEquals(18, $this->cache->get('item1'));
    }

    /**
     * @test
     */
    public function it_gets_a_numeric_item()
    {
        $this->cache->set('item1', 18, 1);

        $this->assertEquals(18, $this->cache->get('item1'));
    }

    /**
     * @test
     */
    public function it_gets_an_object_item()
    {
        $obj = new stdClass();
        $obj->name = "Jesus";

        $this->cache->set('item1', $obj, 10);

        $item = $this->cache->get('item1');

        $this->assertEquals($obj, $item);
    }

    /**
     * @test
     */
    public function it_checks_if_an_item_exists()
    {
        $this->cache->set('nullItem', null, 10);
        $this->assertFalse($this->cache->exists('non-existing'));

        $this->cache->set('someItem', 'someValue', 10);

        $this->assertTrue($this->cache->exists('someItem'));
        $this->assertTrue($this->cache->exists('nullItem'));
    }

    /**
     * @test
     */
    public function it_increments_a_value()
    {
        $this->cache->set('counter', 1, 10);
        $this->cache->set('counterByTwo', 1, 10);

        $this->cache->increment('counter');
        $this->cache->increment('counterByTwo', 2);

        $this->assertEquals(2, $this->cache->get('counter'));
        $this->assertEquals(3, $this->cache->get('counterByTwo'));
    }

    /**
     * @test
     */
    public function it_decrements_a_value()
    {
        $this->cache->set('counter', 10, 10);
        $this->cache->set('counterByTwo', 10, 10);

        $this->cache->decrement('counter');
        $this->cache->decrement('counterByTwo', 2);

        $this->assertEquals(9, $this->cache->get('counter'));
        $this->assertEquals(8, $this->cache->get('counterByTwo'));
    }

    /**
     * @test
     */
    public function it_tests_the_prefix()
    {
        $this->cache->setPrefix("jesus");

        $this->assertEquals("jesus", $this->cache->getPrefix());
    }

    /**
     * @test
     */
    public function it_deletes_an_item()
    {
        $this->cache->set('item1', 'some value', 10);
        $this->cache->set('item2', 'some other value', 10);
        $this->cache->set('item3', 'some other value', 10);

        $this->cache->delete('item1');
        $this->cache->delete('item3');

        $this->assertFalse($this->cache->exists('item1'));
        $this->assertTrue($this->cache->exists('item2'));
        $this->assertFalse($this->cache->exists('item3'));
    }

    /**
     * @test
     */
    public function it_sets_many_items()
    {
        $this->cache->setMany([
            'item1' => 'value1',
            'item2' => 10
        ], 10);

        $this->assertTrue($this->cache->exists('item1'));
        $this->assertTrue($this->cache->exists('item2'));

        $this->assertEquals('value1', $this->cache->get('item1'));
        $this->assertEquals(10, $this->cache->get('item2'));
    }

    /**
     * @test
     */
    public function it_gets_many_items()
    {
        $this->cache->setMany([
            'item1' => 'value1',
            'item2' => 10
        ], 10);

        $this->assertEquals(['value1', 10], $this->cache->getMany(['item1', 'item2']));
    }

    /**
     * @test
     */
    public function it_pulls_an_item()
    {
        $this->cache->set('item1', 'value', 10);

        $value = $this->cache->pull('item1');

        $this->assertEquals('value', $value);
        $this->assertFalse($this->cache->exists('item1'));
    }
}
