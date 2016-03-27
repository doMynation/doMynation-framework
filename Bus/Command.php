<?php

namespace Domynation\Bus;

use Domynation\Utils\ProtectedGetterTrait;

abstract class Command
{
    /**
     * Grants "magic" getters to command fields.
     */
    use ProtectedGetterTrait;
}