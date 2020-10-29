<?php

declare(strict_types=1);

namespace Domynation\Search;

use Assert\Assertion;
use InvalidArgumentException;

/**
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class SearchRequest
{
    public const ORDER_ASC  = 'asc';
    public const ORDER_DESC = 'desc';

    /**
     * @var string[]
     */
    private array $filters;
    private int $offset;
    private int $limit;
    private ?string $sortField;
    private ?string $sortOrder;
    private bool $isPaginated;

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
            throw new InvalidArgumentException("Invalid limit");
        }

        if ($offset < 0) {
            throw new InvalidArgumentException("Invalid offset");
        }

        $this->filters = $filters;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->sortField = $sortField;
        $this->sortOrder = $sortOrder;
        $this->isPaginated = $isPaginated;
    }

    public static function make(): SearchRequestBuilder
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
        $inputFilters = !empty($queryStrings['filters']) ? $queryStrings['filters'] : [];

        // Parse filters
        $filters = array_fold($inputFilters, function (array $acc, string $name, $value) {
            if (is_string($value)) {
                $trimmedValue = trim($value);
                if ($trimmedValue !== '') {
                    $acc[$name] = $trimmedValue;
                }
            } else {
                $acc[$name] = $value;
            }

            return $acc;
        });

        // Determine pagination
        $limit = !empty($queryStrings['limit']) ? (int)$queryStrings['limit'] : 25;
        $page = !empty($queryStrings['page']) && is_numeric($queryStrings['page']) && $queryStrings['page'] > 0 ? (int)$queryStrings['page'] : 1;
        $offset = $limit * ($page - 1);

        // Determine sorting
        $sort = null;
        $order = self::ORDER_ASC;

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
     * @return array|string[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getSortField(): ?string
    {
        return $this->sortField;
    }

    public function getSortOrder(): ?string
    {
        return $this->sortOrder;
    }

    public function isPaginated(): bool
    {
        return $this->isPaginated;
    }

    public function isSorted(): bool
    {
        return $this->sortField !== null;
    }

    public function hasLimit(): bool
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
    public function sort($field, $order = SearchRequest::ORDER_ASC)
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