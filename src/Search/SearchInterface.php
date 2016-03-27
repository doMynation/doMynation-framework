<?php

namespace Domynation\Search;

interface SearchInterface
{

    /**
     * @param $needle
     * @param $index
     * @param array $types
     * @param array $options
     *
     * @return \Solarius\Infrastructure\SearchResult
     */
    public function search($needle, $index, array $types, array $options = []);

    public function index($index, $type, $documentId, $data);

    public function update($index, $type, $documentId, $data);
}