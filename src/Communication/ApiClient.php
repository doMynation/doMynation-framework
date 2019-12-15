<?php

namespace Domynation\Communication;

use Domynation\Exceptions\ApiException;
use GuzzleHttp\Client;

/**
 * @package Domynation\Communication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class ApiClient
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * ApiClient constructor.
     *
     * @param $baseUrl
     * @param $headers
     * @param array $defaultQueryParameters
     */
    public function __construct($baseUrl, $headers, $defaultQueryParameters = [])
    {
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'headers'  => $headers,
            'defaults' => [
                'query' => $defaultQueryParameters,
            ],
        ]);
    }

    /**
     * Prepares the query with the default parameters
     *
     * @param array $options
     *
     * @return array
     */
    private function prepareQuery($options = [])
    {
        return array_merge($this->client->getConfig('defaults')['query'], $options);
    }


    /**
     * Sends a get request to the provided url
     *
     * @param $url
     * @param array $data
     *
     * @return mixed
     * @throws \Domynation\Exceptions\ApiException
     */
    public function get($url, $data = [])
    {
        try {
            $response = $this->client->get($url, [
                'query' => $this->prepareQuery($data),
            ]);

            $json = json_decode($response->getBody(), true);

            if (array_key_exists('error', $json)) {
                throw new ApiException($json['error']['message'], $json['error']['code']);
            }

            return $json;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new ApiException("Not found", $e->getResponse()->getStatusCode());
        }
    }


    /**
     * Sends a post request to the provided url
     *
     * @param $url
     * @param array $postData
     * @param array $getData
     *
     * @return mixed
     * @throws \Domynation\Exceptions\ApiException
     */
    public function post($url, $postData = [], $getData = [])
    {
        try {
            $response = $this->client->post($url, [
                'query'       => $this->prepareQuery($getData),
                'form_params' => $postData,
            ]);

            $json = json_decode($response->getBody(), true);

            if (array_key_exists('error', $json)) {
                throw new ApiException($json['error']['message'], $json['error']['code']);
            }

            return $json;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new ApiException("Not found", $e->getResponse()->getStatusCode());
        }
    }
}