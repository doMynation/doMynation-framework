<?php

namespace Domynation\Contracts;

interface HydratorInterface
{
    /**
     * Hydrates the data.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function hydrate($data = []);

    /**
     * Reverses the hydration process.
     *
     * @param mixed $object
     *
     * @return array
     */
    public function extract($object);
}