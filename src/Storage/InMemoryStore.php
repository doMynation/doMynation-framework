<?php

namespace Domynation\Storage;

/**
 * Class InMemoryStore
 *
 * @package Domynation\Storage
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
class InMemoryStore implements StoreInterface
{

    /**
     * @var array
     */
    private $store;

    /**
     * InMemoryStore constructor.
     *
     * @param array $initialData
     */
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
    public function exists($key)
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