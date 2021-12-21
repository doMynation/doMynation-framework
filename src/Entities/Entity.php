<?php

declare(strict_types=1);

namespace Domynation\Entities;

use Domynation\Contracts\Arrayable;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

/**
 * Class Entity
 *
 * A generic entity inherited by all domain model classes.
 *
 * @package Domynation\Entities
 */
abstract class Entity implements Arrayable, JsonSerializable
{
    public static function generateId(): string
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    abstract public function toArray(): array;
}