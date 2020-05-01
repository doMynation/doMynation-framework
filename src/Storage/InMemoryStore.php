<?php

declare(strict_types=1);

namespace Domynation\Storage;

/**
 * Class InMemoryStore
 *
 * @package Domynation\Storage
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
class InMemoryStore implements StoreInterface
{
    private array $store;

    public function __construct(array $initialData = [])
    {
        $this->store = $initialData;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return array_key_exists($key, $this->store) ? $this->store[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->store;
    }

    /**
     * {@inheritdoc}
     */
    public function pull($key)
    {
        $value = $this->get($key);

        $this->delete($key);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->store[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key): bool
    {
        return isset($this->store[$key]) || array_key_exists($key, $this->store);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        unset($this->store[$key]);
    }
}