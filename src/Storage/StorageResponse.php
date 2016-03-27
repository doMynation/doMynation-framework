<?php

namespace Domynation\Storage;

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

    public function __construct($name, $url)
    {
        $this->name = $name;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}