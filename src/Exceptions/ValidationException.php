<?php

namespace Domynation\Exceptions;

final class ValidationException extends \Exception
{

    /**
     * The list of errors.
     *
     * @var array|string
     */
    protected $errors;

    public function __construct($errors)
    {
        $this->errors = is_array($errors) ? $errors : [$errors];
    }

    public function errors()
    {
        return $this->errors;
    }
}