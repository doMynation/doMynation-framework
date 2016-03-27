<?php

namespace Domynation\Utils;

trait SerializerTrait
{
    public function __invoke(...$args)
    {
        return call_user_func_array([$this, 'serialize'], $args);
    }
}
