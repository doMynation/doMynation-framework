<?php

namespace Domynation\Exceptions;

/**
 * Class ValidationException
 *
 * @package Domynation\Exceptions
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class ValidationException extends \Exception
{

    /**
     * The list of errors.
     *
     * @var array|string
     */
    protected $errors;

    /**
     * ValidationException constructor.
     *
     * @param string $errors
     */
    public function __construct($errors)
    {
        $this->errors = is_array($errors) ? $errors : [$errors];
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}