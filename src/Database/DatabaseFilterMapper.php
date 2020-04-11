<?php

namespace Domynation\Database;

/**
 * A class that maps the filter names to their implementation.
 *
 * @package Domynation\Database
 * @author Dominique Sarrazin <domynation@gmail.com>
 * @deprecated Use `DatabaseFilterMapperSimplified` instead
 */
final class DatabaseFilterMapper implements DatabaseFilterMapperInterface
{
    private string $namespace;
    private array $allowedFilters;

    public function __construct(string $namespace, array $allowedFilters)
    {
        $this->namespace = $namespace;
        $this->allowedFilters = $allowedFilters;
    }

    /**
     * Maps an array of string based filters to their
     * corresponding classes.
     *
     * @param array $filters
     *
     * @return DatabaseFilter[]
     */
    public function map(array $filters): array
    {
        $classes = [];

        foreach ($filters as $name => $value) {
            if (in_array($name, $this->allowedFilters)) {
                $className = $this->resolveClassName($name);

                if (class_exists($className)) {
                    $isValid = call_user_func_array($className . '::validate', [$value]);

                    if ($isValid) {
                        $classes[] = call_user_func_array($className . '::fromForm', [$value]);
                    }
                }
            }
        }

        return $classes;
    }

    /**
     * Resolves the filter class based on its name.
     *
     * @param string $name
     *
     * @return string
     */
    private function resolveClassName(string $name): string
    {
        return $this->namespace . '\\' . ucfirst($name) . 'Filter';
    }
}