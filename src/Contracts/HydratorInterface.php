<?php

namespace Domynation\Contracts;

/**
 * Interface HydratorInterface
 *
 * @package Domynation\Contracts
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
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