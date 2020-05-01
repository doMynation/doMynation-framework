<?php

declare(strict_types=1);

namespace Domynation\Exceptions;

use Exception;

/**
 * Class ValidationException
 *
 * @package Domynation\Exceptions
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class ValidationException extends Exception
{
    /**
     * The list of errors.
     *
     * @var array
     */
    protected array $errors;

    /**
     * ValidationException constructor.
     *
     * @param array|string $errors
     */
    public function __construct($errors)
    {
        $this->errors = is_array($errors) ? $errors : [$errors];
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}