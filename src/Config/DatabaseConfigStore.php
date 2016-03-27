<?php

namespace Domynation\Core;

use Doctrine\DBAL\Connection;

final class DatabaseConfigStore implements ConfigInterface
{

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Fetches all the items in the store.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->db->fetchAll('select code, value from configurations');
    }

    /**
     * Deletes an item from the store.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function delete($key)
    {
        $this->db->delete('configuration', ['code' => $key]);
    }

    /**
     * Checks if an item exists in the store.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists($key)
    {
        $result = $this->db->executeQuery('select id from configurations where code = ?', [$key])->fetch();

        return $result !== false;
    }

    /**
     * Retrieves an item from the store.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->db->executeQuery('select value from configurations where code = ?', [$key])->fetchColumn();
    }

    /**
     * Sets an item in the store.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value)
    {
        if ($this->exists($key)) {
            $this->db->update('configurations', ['value' => $value], ['code' => $key]);

            return;
        }

        // Insert it
        $this->db->insert('configurations', [
            'code'  => $key,
            'value' => $value
        ]);
    }

    /**
     * Retrieves an item from the store and deletes it.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function pull($key)
    {
        // Get the item
        $value = $this->get($key);

        // Delete the item
        $this->delete($key);

        return $value;
    }
}