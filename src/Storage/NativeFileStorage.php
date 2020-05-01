<?php

declare(strict_types=1);

namespace Domynation\Storage;

use Ramsey\Uuid\Uuid;

/**
 * @package Domynation\Storage
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class NativeFileStorage implements StorageInterface
{
    /**
     * The folder where files are stored.
     *
     * @var string
     */
    private string $folder;

    /**
     * The URI fragment to access the files.
     *
     * @var string
     */
    private string $uri;

    public function __construct(string $folder, string $uri)
    {
        $this->folder = $folder;
        $this->uri = $uri;
    }

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
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        $newFileName = Uuid::uuid4() . '.' . $ext;

        // Move the file
        rename($filePath, $this->folder . '/' . $newFileName);

        return new StorageResponse($newFileName, "{$this->uri}/{$newFileName}");
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