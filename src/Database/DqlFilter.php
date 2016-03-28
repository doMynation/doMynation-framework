<?php

namespace Domynation\Database;

use Doctrine\ORM\QueryBuilder;

interface DqlFilter
{

    /**
     * Applies the filter to a DQL query.
     * (Side effecting)
     *
     * @param \Doctrine\ORM\QueryBuilder $builder
     */
    public function applyDql(QueryBuilder $builder);
}