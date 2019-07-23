<?php

namespace Domynation\Search;

/**
 * Interface SearchInterface
 *
 * @todo: Work in progress. Don't use this.
 *
 * @package Domynation\Search
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface SearchInterface
{

    public function search($needle, $index, array $types, array $options = []);

    public function index($index, $type, $documentId, $data);

    public function update($index, $type, $documentId, $data);
}