<?php

namespace Domynation\Utils;

/**
 * Class SerializerTrait
 *
 * @package Domynation\Utils
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
trait SerializerTrait
{
    public function __invoke(...$args)
    {
        return call_user_func_array([$this, 'serialize'], $args);
    }
}
