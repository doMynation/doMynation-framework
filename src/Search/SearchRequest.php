<?php

namespace Domynation\Search;

use Assert\Assertion;

/**
 * Class SearchRequest
 *
 * @package Domynation\Search
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class SearchRequest
{
    const ORDER_ASC  = 'asc';
    const ORDER_DESC = 'desc';

    /**
     * @var string[]
     */
    private $filters;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var string
     */
    private $sortField;

    /**
     * @var string
     */
    private $sortOrder;

    /**
     * @var bool
     */
    private $isPaginated;

    /**
     * SearchRequest constructor.
     *
     * @param array $filters
     * @param int $offset
     * @param int $limit
     * @param null|string $sortField
     * @param null|string $sortOrder
     * @param bool $isPaginated
     */
    public function __construct(array $filters, int $offset, int $limit, ?string $sortField, ?string $sortOrder, bool $isPaginated)
    {
        Assertion::nullOrString($sortField, "Invalid sort field");
        Assertion::choice($sortOrder, [null, self::ORDER_ASC, self::ORDER_DESC], "Invalid sort order");

        if ($limit < 0) {
            throw new \InvalidArgumentException("Invalid limit");
        }

        if ($offset < 0) {
            throw new \InvalidArgumentException("Invalid offset");
        }

        $this->filters = $filters;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->sortField = $sortField;
        $this->sortOrder = $sortOrder;
        $this->isPaginated = $isPaginated;
    }

    /**
     * @return \Domynation\Search\SearchRequestBuilder
     */
    public static function make()
    {
        return new SearchRequestBuilder;
    }

    /**
     * @param array $queryStrings
     * @param bool $isPaginated
     *
     * @return \Domynation\Search\SearchRequest
     */
    public static function fromQueryStrings(array $queryStrings, bool $isPaginated = true): self
    {
        // Parse filters
        $filters = array_fold($queryStrings['filters'] ?? [], function (array $acc, string $name, $value) {
            if (is_string($value)) {
                $trimmedValue = trim($value);
                if ($trimmedValue !== "") {
                    $acc[$name] = $trimmedValue;
                }
            } else {
                $acc[$name] = $value;
            }

            return $acc;
        });

        // Determine pagination
        $limit = !empty($queryStrings['limit']) ? (int)$queryStrings['limit'] : 25;
        $page = !empty($queryStrings['page']) && is_numeric($queryStrings['page']) ? (int)$queryStrings['page'] : 1;
        $offset = $limit * ($page - 1);

        // Determine sorting
        $sort = null;
        $order = 'asc';

        if (!empty($queryStrings['sortField'])) {
            $sort = $queryStrings['sortField'];
            $order = !empty($queryStrings['sortOrder']) ? $queryStrings['sortOrder'] : null;
        }

        return self::make()
            ->filter($filters)
            ->take($limit)
            ->skip($offset)
            ->sort($sort, $order)
            ->paginate($isPaginated)
            ->get();
    }

    /**
     * @return string[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @return string
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @return boolean
     */
    public function isPaginated()
    {
        return $this->isPaginated;
    }

    /**
     * @return bool
     */
    public function isSorted()
    {
        return !is_null($this->sortField);
    }

    /**
     * @return bool
     */
    public function hasLimit()
    {
        return $this->limit > 0;
    }
}

final class SearchRequestBuilder
{
    private $filters;
    private $offset;
    private $limit;
    private $sortField;
    private $sortOrder;
    private $isPaginated;

    public function __construct()
    {
        $this->filters = [];
        $this->offset = 0;
        $this->limit = 0;
        $this->sortField = null;
        $this->sortOrder = null;
        $this->isPaginated = false;
    }

    /**
     * Limits the number of results to $limit.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function take($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Sets pagination.
     *
     * @param bool $isPaginated
     *
     * @return $this
     */
    public function paginate(bool $isPaginated = true)
    {
        $this->isPaginated = $isPaginated;

        return $this;
    }

    /**
     * Sets the offset.
     *
     * @param int $offset
     *
     * @return $this
     */
    public function skip($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Sorts the results according to a field.
     *
     * @param string $field
     * @param string $order ["asc" or "desc"]
     *
     * @return $this
     */
    public function sort($field, $order = 'asc')
    {
        $this->sortField = $field;
        $this->sortOrder = $order;

        return $this;
    }

    /**
     * Filters the results according to a field.
     *
     * @param string|array $field
     * @param mixed $value
     *
     * @return $this
     */
    public function filter($field, $value = null)
    {
        if (is_array($field)) {
            $this->filters = array_merge($this->filters, $field);

            return $this;
        }

        $this->filters[$field] = $value;

        return $this;
    }


    /**
     * Builds the SearchRequest and returns it.
     *
     * @return \Domynation\Search\SearchRequest
     */
    public function get()
    {
        return new SearchRequest(
            $this->filters,
            $this->offset,
            $this->limit,
            $this->sortField,
            $this->sortOrder,
            $this->isPaginated
        );
    }
}