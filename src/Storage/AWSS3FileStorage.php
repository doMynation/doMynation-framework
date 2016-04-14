<?php

namespace Domynation\Storage;

use Aws\S3\S3Client;
use Ramsey\Uuid\Uuid;

final class AWSS3FileStorage implements StorageInterface
{

    /**
     * @var \Aws\S3\S3Client
     */
    private $client;

    /**
     * AWSS3FileStorage constructor.
     *
     * @param string $region
     * @param string $apiKey
     * @param string $privateKey
     */
    public function __construct($region, $apiKey, $privateKey)
    {
        $this->client = new S3Client([
            'version'     => 'latest',
            'region'      => $region,
            'credentials' => [
                'key'    => $apiKey,
                'secret' => $privateKey
            ]
        ]);
    }

    /**
     * Retrieves a file from the storage.
     *
     * @param string $key
     * @param array $data
     *
     * @return mixed
     */
    public function get($key, $data = [])
    {
        // TODO: Implement get() method.
    }

    /**
     * Fetches all the files in the storage.
     *
     * @param array $data
     *
     * @return array
     */
    public function getAll($data = [])
    {
        // TODO: Implement getAll() method.
    }

    /**
     * Puts a file in the storage.
     *
     * @param string $filePath
     * @param array $data
     *
     * @return StorageResponse
     */
    public function put($filePath, $data = [])
    {
        $fileInfo = pathinfo($filePath);

        $key = Uuid::uuid4() . '.' . $fileInfo['extension'];

        $result = $this->client->putObject([
            'Bucket'     => $data['bucket'],
            'Key'        => $key,
            'SourceFile' => $filePath,
            'Metadata'   => [
                'originalName' => $fileInfo['basename']
            ]
        ]);

        return new StorageResponse($key, $result['ObjectURL']);
    }

    /**
     * Deletes a file from the storage.
     *
     * @param string $key
     * @param array $data
     *
     * @return mixed
     */
    public function delete($key, $data = [])
    {
        // TODO: Implement delete() method.
    }

    /**
     * Checks if a file exists in the storage.
     *
     * @param string $key
     * @param array $data
     *
     * @return mixed
     */
    public function exists($key, $data = [])
    {
        // TODO: Implement exists() method.
    }
}