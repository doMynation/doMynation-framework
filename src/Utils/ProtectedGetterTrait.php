<?php

namespace Domynation\Utils;

/**
 * Class ProtectedGetterTrait
 *
 * This grants access to private/protected members of a class as if they were public.
 * This is particularly useful for DTO classes such as plain Command objects where we want to
 * enforce immutability but we don't want to have to generate setters individualy.
 *
 * @package Domynation\Utils
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
trait ProtectedGetterTrait
{

    public function __isset($key)
    {
        return isset($this->$key);
    }

    public function __get($key)
    {
        return $this->$key;
    }
}