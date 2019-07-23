<?php

namespace Domynation\Entities;

/**
 * Class EntityRegistry
 *
 * A registry of all the entities in the application and their configurations.
 *
 * @package Domynation\Entities
 */
final class EntityRegistry
{
    protected $definitions;

    /**
     * EntityRegistry constructor.
     */
    public function __construct()
    {
        $this->definitions = [];
    }

    /**
     * Adds an entity definition to the registry.
     *
     * @param array $data
     */
    public function addDefinition($data)
    {
        $this->definitions[] = $data;
    }

    /**
     * Fetches the definition for a specific entity.
     *
     * @param string $type
     *
     * @return array
     */
    public function getDefinition($type)
    {
        return isset($this->definitions[$type]) ? $this->definitions[$type] : null;
    }

    /**
     * @return array
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }
}