<?php

namespace Domynation\Storage;

/**
 * Class StorageResponse
 *
 * @package Domynation\Storage
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class StorageResponse
{
    /**
     * @var string The name of the hosted file.
     */
    private $name;

    /**
     * @var string The url to the file.
     */
    private $url;

    /**
     * StorageResponse constructor.
     *
     * @param string $name
     * @param string $url
     */
    public function __construct(string $name, string $url)
    {
        $this->name = $name;
        $this->url  = $url;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}