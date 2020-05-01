<?php

declare(strict_types=1);

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
     * The name of the hosted file.
     *
     * @var string
     */
    private string $name;

    /**
     * The url to the file.
     *
     * @var string
     */
    private string $url;

    public function __construct(string $name, string $url)
    {
        $this->name = $name;
        $this->url = $url;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}