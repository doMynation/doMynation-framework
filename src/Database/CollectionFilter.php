<?php

namespace Domynation\Database;

use Doctrine\Common\Collections\Criteria;

/**
 * @package Domynation\Database
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface CollectionFilter
{

    /**
     * Returns a criteria to filter a doctrine `Selectable`.
     *
     * @return \Doctrine\Common\Collections\Criteria
     */
    public function getCriteria(): Criteria;
}
