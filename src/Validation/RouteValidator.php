<?php

namespace Domynation\Validation;

/**
 * Class RouteValidator
 *
 * @package Domynation\Validation
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
abstract class RouteValidator
{

    /**
     * Validates the inputs and returns true if all inputs
     * pass the validation process.
     *
     * @param array $inputs
     *
     * @return bool
     */
    abstract public function validate($inputs);
}