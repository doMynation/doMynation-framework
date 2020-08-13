<?php

declare(strict_types=1);

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
    public function put(UploadedFile $file, $data = [])
    {
        return new StorageResponse($file->getPath(), "/tests/{$file->getOriginalName()}");
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
    public function exists($key, $data = []): bool
    {
        return file_exists("{$this->folder}/{$key}");
    }
}