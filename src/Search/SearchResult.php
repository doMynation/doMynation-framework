<?php

declare(strict_types=1);

namespace Domynation\Search;

/**
 * The response of a search request.
 *
 * @package Domynation\Search
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class SearchResult implements \JsonSerializable
{
    public function __construct(private array $hits, private int $count, private bool $isPaginated, private int $pageSize)
    {
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

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getPages(): int
    {
        if (!$this->isPaginated) {
            return 1;
        }
        
        return (int)ceil($this->count / $this->pageSize);
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }
    
    public function toArray(): array
    {
        return [
            'count' => $this->count,
            'results' => $this->hits,
            'isPaginated' => $this->isPaginated,
            'pageSize' => $this->pageSize,
            'pages' => $this->getPages(),
        ];
    }
}