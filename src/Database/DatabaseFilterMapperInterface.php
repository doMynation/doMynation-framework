<?php

declare(strict_types=1);

namespace Domynation\Database;

/**
 * @package Domynation\Database
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface DatabaseFilterMapperInterface
{
    public function map(array $fiters): array;
}