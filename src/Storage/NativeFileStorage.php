<?php

namespace Domynation\Storage;

use Ramsey\Uuid\Uuid;

/**
 * Class NativeFileStorage
 *
 * @package Domynation\Storage
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class NativeFileStorage implements StorageInterface
{
    /**
     * @var string the folder where files are stored.
     */
    private $folder;

    /**
     * @var string the URI fragment to access the files.
     */
    private $uri;

    /**
     * NativeFileStorage constructor.
     *
     * @param string $folder
     * @param string $uri
     */
    public function __construct($folder, $uri)
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

        // Move it
        rename($filePath, "{$this->folder}/{$newFileName}");

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
    public function exists($key, $data = [])
    {
        return file_exists("{$this->folder}/{$key}");
    }
}