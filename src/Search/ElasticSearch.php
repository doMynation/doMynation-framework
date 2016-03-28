<?php

namespace Domynation\Search;

use Elasticsearch\Client;
use Solarius\Infrastructure\SearchResult;

/**
 * Class ElasticSearch
 *
 * @todo: Still in progress.
 *
 * @package Domynation\Search
 */
final class ElasticSearch implements SearchInterface
{

    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function search($needle, $index, array $types, array $options = [])
    {
        $response = $this->client->search([
            'index' => $index,
            'type'  => $types,
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query'  => $needle,
                        'type'   => 'phrase_prefix',
                        'fields' => ['_all']
                    ]
                ]
            ]
        ]);

        $hits = array_map(function ($hit) {
            return [
                'id'    => $hit['_id'],
                'index' => $hit['_index'],
                'type'  => $hit['_type'],
                'data'  => $hit['_source']
            ];
        }, $response['hits']['hits']);

        return new SearchResult($hits, $response['hits']['total']);
    }

    public function index($index, $type, $documentId, $data)
    {
        $this->client->index([
            'index' => $index,
            'type'  => $type,
            'id'    => $documentId,
            'body'  => utf8_encode_array($data)
        ]);
    }

    public function update($index, $type, $documentId, $data)
    {
        $this->client->index([
            'index' => $index,
            'type'  => $type,
            'id'    => $documentId,
            'body'  => utf8_encode_array($data)
        ]);
    }
}