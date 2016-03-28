<?php

use Domynation\Search\SearchRequest;

class SearchRequestTest extends PHPUnit_Framework_TestCase
{
    function invalidArguments()
    {
        return [
            [10, 0, 100, 'test', 'asc', false],
            [null, 0, 100, 'test', 'asc', false],
            [true, 0, 100, 'test', 'asc', false],
            ["string", 0, 100, 'test', 'asc', false],
            [new stdClass, 0, 100, 'test', 'asc', false],

            [[], -40, 100, 'test', 'asc', false],
            [[], "string", 100, 'test', 'asc', false],
            [[], null, 100, 'test', 'asc', false],
            [[], false, 100, 'test', 'asc', false],
            [[], new stdClass, 100, 'test', 'asc', false],

            [[], 0, "string", 'test', 'asc', false],
            [[], 0, null, 'test', 'asc', false],
            [[], 0, false, 'test', 'asc', false],
            [[], 0, new stdClass, 'test', 'asc', false],
            [[], 0, -50, 'test', 'asc', false],

            [[], 0, 100, 'test', -40, false],
            [[], 0, 100, 'test', false, false],
            [[], 0, 100, 'test', new stdClass, false],
            [[], 0, 100, 'test', "string", false],

            [[], 0, 100, 'test', "asc", -40],
            [[], 0, 100, 'test', "asc", null],
            [[], 0, 100, 'test', "asc", new stdClass]
        ];
    }


    /**
     * @test
     */
    public function it_initializes_with_given_values()
    {
        $req = new SearchRequest([], 0, 100, 'createdAt', SearchRequest::ORDER_ASC, false);

        $this->assertEmpty($req->getFilters());
        $this->assertEquals(0, $req->getOffset());
        $this->assertEquals(100, $req->getLimit());
        $this->assertEquals('createdAt', $req->getSortField());
        $this->assertEquals('asc', $req->getSortOrder());
        $this->assertFalse($req->isPaginated());
    }

    /**
     * @test
     */
    public function it_initializes_with_default_values()
    {
        $req = SearchRequest::make()->get();

        $this->assertEmpty($req->getFilters());
        $this->assertEquals(0, $req->getOffset());
        $this->assertEquals(0, $req->getLimit());
        $this->assertNull($req->getSortField());
        $this->assertNull($req->getSortOrder());
        $this->assertFalse($req->isPaginated());
    }

    /**
     * @test
     * @dataProvider invalidArguments
     * @expectedException \Exception
     */
    public function it_fails_when_passing_an_invalid_sort_order($filters, $offset, $limit, $sortField, $sortOrder, $isPaginated)
    {
        new SearchRequest($filters, $offset, $limit, $sortField, $sortOrder, $isPaginated);
    }

    /**
     * @test
     */
    public function it_adds_a_single_filter()
    {
        $req = SearchRequest::make()
            ->filter('name', 'John')
            ->get();

        $this->assertEquals(['name' => 'John'], $req->getFilters());
    }

    /**
     * @test
     */
    public function it_adds_multiple_filters()
    {
        $expected = [
            'name'       => 'Dom',
            'id'         => 18,
            'doMynation' => 'isGod',
        ];

        $filters = [
            'name' => 'John',
            'id'   => 18
        ];

        $req = SearchRequest::make()
            ->filter('doMynation', 'isGod')
            ->filter($filters)
            ->filter('name', 'Dom')
            ->get();

        $this->assertEquals($expected, $req->getFilters());
    }

    /**
     * @test
     */
    public function it_sets_the_offset()
    {
        $req = SearchRequest::make()->skip(10)->get();

        $this->assertEquals(10, $req->getOffset());
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_fails_when_setting_a_negative_offset()
    {
        SearchRequest::make()->skip(-80)->get();
    }

    /**
     * @test
     */
    public function it_sets_the_limit()
    {
        $req = SearchRequest::make()->take(10)->get();

        $this->assertEquals(10, $req->getLimit());
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_fails_when_setting_a_negative_limit()
    {
        SearchRequest::make()->take(-80)->get();
    }

    /**
     * @test
     */
    public function it_paginates_the_search_request()
    {
        $req = SearchRequest::make()->paginate()->get();

        $this->assertTrue($req->isPaginated());
    }
}
