<?php

namespace Domynation\Contracts;

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