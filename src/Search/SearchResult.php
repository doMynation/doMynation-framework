<?php

declare(strict_types=1);

namespace Domynation\Search;

/**
 * The response of a search request.
 *
 * @package Domynation\Search
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class SearchResult
{
    private array $hits;
    private int $count;

    public function __construct(array $hits, int $count)
    {
        $this->hits = $hits;
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
    public function getRows(): array
    {
        return $this->hits;
    }

    public function getHits(): array
    {
        return $this->hits;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}