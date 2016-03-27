<?php

namespace Domynation\Entities;

use Domynation\Contracts\Arrayable;

/**
 * Class Entity
 *
 * A generic entity inherited by all domain model classes.
 *
 * @package Domynation\Entities
 */
abstract class Entity implements Arrayable, \JsonSerializable
{

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    abstract public function toArray();
}