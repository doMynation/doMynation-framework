<?php

namespace Domynation\Bus;

use Domynation\Utils\ProtectedGetterTrait;

/**
 * A command.
 *
 * @package Domynation\Bus
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
abstract class Command
{
    /**
     * Grants "magic" getters to command fields.
     */
    use ProtectedGetterTrait;
}