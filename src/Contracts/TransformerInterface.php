<?php

namespace Domynation\Contracts;

/**
 * Interface TransformerInterface
 *
 * @package Domynation\Contracts
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface TransformerInterface
{
    /**
     * Transforms an entity into another representation.
     *
     * @param mixed $object
     *
     * @return array
     */
    public function transform($object);

    /**
     * Transforms a collection of entities into another represention.
     *
     * @param $collection
     *
     * @return mixed
     */
    public function transformCollection($collection);
}