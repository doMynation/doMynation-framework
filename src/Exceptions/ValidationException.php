<?php

declare(strict_types=1);

namespace Domynation\Exceptions;

use Exception;

/**
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class ValidationException extends Exception
{
    public function __construct(protected array $errors)
    {
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}