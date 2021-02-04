<?php

declare(strict_types=1);

namespace Domynation\Storage;

use Assert\Assertion;
use Symfony\Component\Mime\MimeTypes;

/**
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class UploadedFile
{
    private string $originalName;
    private string $path;
    private int $size;
    private string $mimeType;

    public function __construct(string $originalName, string $path, int $size, string $mimeType)
    {
        Assertion::notEmpty($originalName);
        Assertion::notEmpty($size);
        Assertion::notEmpty($mimeType);
        Assertion::file($path);

        $this->originalName = $originalName;
        $this->path = $path;
        $this->size = $size;
        $this->mimeType = $mimeType;
    }

    public static function fromPath(string $path): self
    {
        return new self(
            pathinfo($path, PATHINFO_BASENAME),
            $path,
            filesize($path),
            MimeTypes::getDefault()->guessMimeType($path)
        );
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getExtension(): string
    {
        return mb_strtolower(pathinfo($this->originalName, PATHINFO_EXTENSION));
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function isImage(): bool
    {
        return strpos($this->mimeType, 'image/') === 0;
    }
}

