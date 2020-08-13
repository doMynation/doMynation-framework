<?php

declare(strict_types=1);

namespace Domynation\Storage;

use Ramsey\Uuid\Uuid;

/**
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class RackspaceFileStorage implements StorageInterface
{
    private string $username;
    private string $apiKey;

    public function __construct(string $username, string $apiKey)
    {
        $this->username = $username;
        $this->apiKey = $apiKey;
    }

    /**
     * @return \CF_Connection
     */
    private function authenticate(): \CF_Connection
    {
        $auth = new \CF_Authentication($this->username, $this->apiKey);

        $auth->authenticate();

        return new \CF_Connection($auth);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll($data = [])
    {
        if (array_key_exists('container', $data)) {
            throw new \RuntimeException("Container missing");
        }

        $limit = array_key_exists('limit', $data) ? $data['limit'] : 99999;

        $connection = $this->authenticate();

        $container = $connection->get_container($data['container']);
        $items = $container->get_objects($limit, null);

        return array_map(function ($item) {
            return [
                'name' => $item->name,
                'url'  => $item->public_ssl_uri()
            ];
        }, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $data = [])
    {
        if (array_key_exists('container', $data)) {
            throw new \RuntimeException("Container missing");
        }

        $connection = $this->authenticate();

        try {
            $container = $connection->get_container($data['container']);
            $file = $container->get_object($key);
        } catch (\Exception $e) {
            $file = null;
        }

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key, $data = []): bool
    {
        if (array_key_exists('container', $data)) {
            throw new \RuntimeException("Container missing");
        }

        return !is_null($this->get($key, $data));
    }

    /**
     * {@inheritdoc}
     */
    public function put(UploadedFile $file, $data = [])
    {
        if (array_key_exists('container', $data)) {
            throw new \RuntimeException("Container missing");
        }

        $connection = $this->authenticate();
        $container = $connection->get_container($data['container']);

        $fileName = Uuid::uuid4() . '.' . $file->getExtension();

        $object = $container->create_object($fileName);
        $object->load_from_filename($file->getPath());
        $uri = $container->make_public();

        return new StorageResponse($object->name, $object->public_ssl_uri());
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key, $data = [])
    {
        if (array_key_exists('container', $data)) {
            throw new \RuntimeException("Container missing");
        }

        $connection = $this->authenticate();
        $container = $connection->get_container($data['container']);
        $container->delete_object($key);
    }
}