<?php

namespace Domynation\Storage;

/**
 * Interface StorageInterface
 *
 * @package Domynation\Storage
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface StorageInterface
{

    /**
     * Retrieves a file from the storage.
     *
     * @param string $key
     * @param array $data
     *
     * @return mixed
     */
    public function get($key, $data = []);

    /**
     * Fetches all the files in the storage.
     *
     * @param array $data
     *
     * @return array
     */
    public function getAll($data = []);

    /**
     * Puts a file in the storage.
     *
     * @param string $filePath
     * @param array $data
     *
     * @return StorageResponse
     */
    public function put($filePath, $data = []);

    /**
     * Deletes a file from the storage.
     *
     * @param string $key
     * @param array $data
     *
     * @return mixed
     */
    public function delete($key, $data = []);

    /**
     * Checks if a file exists in the storage.
     *
     * @param string $key
     * @param array $data
     *
     * @return mixed
     */
    public function exists($key, $data = []);
}