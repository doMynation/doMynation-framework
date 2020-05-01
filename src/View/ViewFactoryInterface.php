<?php

declare(strict_types=1);

namespace Domynation\View;

/**
 * Interface ViewFactoryInterface
 *
 * @package Domynation\View
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface ViewFactoryInterface
{
    /**
     * Renders a view with its data.
     *
     * @param string $viewName
     * @param array $data
     *
     * @return string
     */
    public function render(string $viewName, $data = []): string;

    /**
     * Adds a namespace and aliases it with the given name.
     *
     * @param string $path
     * @param string $name
     */
    public function addNamespace(string $path, string $name): void;

    /**
     * Adds a global variable that will be injected in all views.
     *
     * @param string $name
     * @param string $value
     */
    public function addGlobal(string $name, string $value): void;

    /**
     * Adds a function to the list of functions available from the views.
     *
     * @param string $name
     * @param callable $closure
     */
    public function addFunction(string $name, callable $closure): void;

    /**
     * Adds a filter to the list of filters available from the views.
     *
     * @param string $name
     * @param callable $closure
     */
    public function addFilter(string $name, callable $closure): void;
}