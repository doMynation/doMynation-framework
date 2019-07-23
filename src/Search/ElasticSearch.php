<?php

namespace Domynation\Search;

use Elasticsearch\Client;

/**
 * Class ElasticSearch
 *
 * @todo: Work in progress. Don't use this.
 *
 * @package Domynation\Search
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class ElasticSearch implements SearchInterface
{

    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    /**
     * ElasticSearch constructor.
     *
     * @param \Elasticsearch\Client $client
     */
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
            'body'  => $data
        ]);
    }

    public function update($index, $type, $documentId, $data)
    {
        $this->client->index([
            'index' => $index,
            'type'  => $type,
            'id'    => $documentId,
            'body'  => $data
        ]);
    }
}