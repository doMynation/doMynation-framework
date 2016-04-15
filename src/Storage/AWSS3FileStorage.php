<?php

namespace Domynation\Storage;

use Aws\S3\Exception\S3Exception;
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
        if (!isset($data['container'])) {
            throw new \RuntimeException("Container missing");
        }

        $response = $this->client->getObject([
            'Bucket' => $data['container'],
            'Key' => $key
        ]);

        return [
            'name' => $key,
            'url' => $response['@metadata']['effectiveUri'],
            'size' => (int)$response['ContentLength'],
            'mimeType' => $response['ContentType'],
            'metadata' => $response['Metadata']
        ];
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
        $response = $this->client->listObjects([
            'Bucket' => $data['container'],
            'MaxKeys' => isset($data['limit']) ? $data['limit'] : 1000
        ]);

        return array_map(function($file) use ($data) {
            return [
                'name' => $file['Key'],
                'size' => $file['Size'],
                'url' => "https://s3.amazonaws.com/{$data['container']}/{$file['Key']}"
            ];
        }, $response['Contents']);
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
        if (!isset($data['container'])) {
            throw new \RuntimeException("Container missing");
        }

        $fileInfo = pathinfo($filePath);

        // Generate a unique name
        $key = Uuid::uuid4() . '.' . $fileInfo['extension'];

        $result = $this->client->putObject([
            'Bucket'     => $data['container'],
            'Key'        => $key,
            'SourceFile' => $filePath,
            'ACL'        => 'public-read',
            'Metadata'   => array_merge($data, [
                'originalName' => $fileInfo['basename']
            ])
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
        if (!isset($data['container'])) {
            throw new \RuntimeException("Container missing");
        }

        $response = $this->client->deleteObject([
            'Bucket'     => $data['container'],
            'Key'        => $key,
        ]);

        return $response;
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
        try {
            $file = $this->get($key, $data);
        } catch (S3Exception $e) {
            return false;
        }

        return true;
    }
}