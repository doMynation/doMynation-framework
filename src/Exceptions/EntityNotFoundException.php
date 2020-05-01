<?php

declare(strict_types=1);

namespace Domynation\Exceptions;

use Exception;

/**
 * Exception thrown when an entity cannot be found in the database.
 *
 * @package Domynation\Exceptions
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class EntityNotFoundException extends Exception
{
}