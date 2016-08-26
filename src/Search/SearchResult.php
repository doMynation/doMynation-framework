<?php

namespace Domynation\Search;

/**
 * The response of a search request.
 *
 * @package Domynation\Search
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class SearchResult
{

    /**
     * @var array
     */
    private $hits;

    /**
     * @var int
     */
    private $count;

    public function __construct(array $hits, $count)
    {
        $this->hits  = $hits;
        $this->count = $count;
    }

    /**
     * Returns the first result as a single row.
     *
     * @return mixed
     * @deprecated
     */
    public function getSingleRow()
    {
        return !empty($this->hits) ? $this->hits[0] : null;
    }

    /**
     * Returns the first hit as the only result.
     *
     * @return mixed
     */
    public function getSingleHit()
    {
        return !empty($this->hits) ? $this->hits[0] : null;
    }

    /**
     * @return array
     *
     * @deprecated
     */
    public function getRows()
    {
        return $this->hits;
    }

    /**
     * @return array
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}