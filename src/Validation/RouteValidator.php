<?php

declare(strict_types=1);

namespace Domynation\Validation;

/**
 * @package Domynation\Validation
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
abstract class RouteValidator
{
    /**
     * Validates the inputs and throws a ValidationException is it fails.
     *
     * @param array $inputs
     *
     * @throws \Domynation\Exceptions\ValidationException
     */
    public function validate($inputs)
    {
    }
}