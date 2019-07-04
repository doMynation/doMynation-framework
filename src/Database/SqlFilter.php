<?php

namespace Domynation\Database;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Interface SqlFilter
 *
 * @package Domynation\Database
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface SqlFilter
{
    /**
     * Applies the filter to a SQL query.
     * (Side effecting)
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $builder
     */
    public function applySql(QueryBuilder $builder);
}