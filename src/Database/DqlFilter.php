<?php

declare(strict_types=1);

namespace Domynation\Database;

use Doctrine\ORM\QueryBuilder;

/**
 * @package Domynation\Database
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface DqlFilter
{

    /**
     * Applies the filter to a DQL query.
     * (Side effecting)
     *
     * @param \Doctrine\ORM\QueryBuilder $builder
     */
    public function applyDql(QueryBuilder $builder): void;
}