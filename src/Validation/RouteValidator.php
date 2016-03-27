<?php

namespace Domynation\Validation;

use Domynation\Utils\MessageRecorderTrait;

abstract class RouteValidator
{

    use MessageRecorderTrait;

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