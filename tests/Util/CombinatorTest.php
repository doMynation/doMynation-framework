<?php

use Domynation\Utils\Combinator;

class CombinatorTest extends PHPUnit_Framework_TestCase
{

    protected $c;

    public function setUp()
    {
        $this->c = new Combinator;
    }


    /**
     * @test
     */
    public function it_initializes_the_combinator()
    {
        $this->assertNotNull($this->c);
    }

    /**
     * @test
     */
    public function it_inserts_a_value()
    {
        $this->c->insert("a", 10);

        $this->assertEquals(10, $this->c->get("a"));
    }

    /**
     * @test
     */
    public function it_combines_the_quantity_when_inserting_an_already_existing_element()
    {
        $this->c->insert("a", 10);
        $this->c->insert("a", 10);

        $this->c->insert("b", 10);
        $this->c->insert("b", 2);
        $this->c->insert("c", 4);

        $this->assertEquals(20, $this->c->get("a"));
        $this->assertEquals(12, $this->c->get("b"));
        $this->assertEquals(4, $this->c->get("c"));
        $this->assertNull($this->c->get("non_existing_key"));
    }

    /**
     * @test
     */
    public function it_checks_if_a_value_exists()
    {
        $this->assertFalse($this->c->exists("a"));
        $this->c->insert("a", 10);
        $this->assertTrue($this->c->exists("a"));
    }

    /**
     * @test
     */
    public function it_returns_the_elements_as_an_array()
    {
        $this->c->insert("a", 10);
        $this->c->insert("b", 5);
        $this->c->insert("b", 3);
        $this->c->insert("c", 1);
        $this->c->insert("d", 0);

        $this->assertEquals([
            "a" => 10,
            "b" => 8,
            "c" => 1,
            "d" => 0,
        ], $this->c->toArray());
    }

    /**
     * @test
     */
    public function it_returns_the_keys_as_an_array()
    {
        $this->c->insert("a", 10);
        $this->c->insert("b", 5);
        $this->c->insert("b", 3);
        $this->c->insert("c", 1);
        $this->c->insert("d", 0);

        $this->assertEquals(["a", "b", "c", "d"], $this->c->getKeys());
    }

    /**
     * @xtest
     * @expectedException InvalidArgumentException
     */
    public function it_fails_when_creating_an_invalid_email_address()
    {
    }
}