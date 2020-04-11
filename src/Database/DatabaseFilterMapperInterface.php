<?php

namespace Domynation\Database;

interface DatabaseFilterMapperInterface
{
    public function map(array $fiters): array;
}