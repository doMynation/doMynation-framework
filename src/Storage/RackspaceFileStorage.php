<?php

namespace Domynation\Storage;

use Ramsey\Uuid\Uuid;

/**
 * Class RackspaceFileStorage
 *
 *
 * @package Domynation\Storage
 */
final class RackspaceFileStorage implements StorageInterface
{

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $apiKey;

    public function __construct($username, $apiKey)
    {
        $this->username = $username;
        $this->apiKey   = $apiKey;
    }

    private function authenticate()
    {
        $auth = new \CF_Authentication($this->username, $this->apiKey);

        $auth->authenticate();

        return new \CF_Connection($auth);
    }

    public function getAll($data = [])
    {
        if (array_key_exists('container', $data)) {
            throw new \RuntimeException("Container missing");
        }

        $limit = array_key_exists('limit', $data) ? $data['limit'] : 99999;

        $connection = $this->authenticate();

        $container = $connection->get_container($data['container']);
        $items     = $container->get_objects($limit, null);

        return array_map(function ($item) {
            return [
                'name' => $item->name,
                'url'  => $item->public_ssl_uri()
            ];
        }, $items);
    }

    public function get($key, $data = [])
    {
        if (array_key_exists('container', $data)) {
            throw new \RuntimeException("Container missing");
        }

        $connection = $this->authenticate();

        try {
            $container = $connection->get_container($data['container']);
            $file      = $container->get_object($key);
        } catch (\Exception $e) {
            $file = null;
        }

        return $file;
    }

    public function exists($key, $data = [])
    {
        if (array_key_exists('container', $data)) {
            throw new \RuntimeException("Container missing");
        }

        return !is_null($this->get($key, $data));
    }

    public function put($filePath, $data = [])
    {
        if (array_key_exists('container', $data)) {
            throw new \RuntimeException("Container missing");
        }

        $connection = $this->authenticate();
        $container  = $connection->get_container($data['container']);

        $ext      = pathinfo($filePath, PATHINFO_EXTENSION);
        $fileName = Uuid::uuid4() . '.' . $ext;

        $object = $container->create_object($fileName);
        $object->load_from_filename($filePath);
        $uri = $container->make_public();

        return [
            'url'  => $object->public_ssl_uri(),
            'name' => $object->name
        ];
    }

    public function delete($key, $data = [])
    {
        if (array_key_exists('container', $data)) {
            throw new \RuntimeException("Container missing");
        }

        $connection = $this->authenticate();
        $container  = $connection->get_container($data['container']);
        $container->delete_object($key);
    }
}