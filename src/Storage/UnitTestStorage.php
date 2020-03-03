<?php

namespace Domynation\Storage;

/**
 * @package Domynation\Storage
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class UnitTestStorage implements StorageInterface
{

    /**
     * {@inheritdoc}
     */
    public function get($key, $data = [])
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getAll($data = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function put($filePath, $data = [])
    {
        $fileInfo = pathinfo($filePath);

        return new StorageResponse($fileInfo['filename'], "/tests/{$fileInfo['filename']}");
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key, $data = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key, $data = [])
    {
        return file_exists("{$this->folder}/{$key}");
    }
}