<?php

namespace Domynation\Database;

final class DatabaseFilterMapper
{

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var array
     */
    private $allowedFilters;

    public function __construct($namespace, $allowedFilters)
    {
        $this->namespace      = $namespace;
        $this->allowedFilters = $allowedFilters;
    }

    /**
     * Maps an array of string based filters to their
     * corresponding classes.
     *
     * @param array $filters
     *
     * @return array
     */
    public function map($filters)
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
     * @return mixed
     */
    private function resolveClassName($name)
    {
        return $this->namespace . '\\' . ucfirst($name) . 'Filter';
    }
}