<?php

namespace Domynation\View;

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
    public function render($viewName, $data = []);

    /**
     * Adds a namespace and aliases it with the given
     * name.
     *
     * @param string $path
     * @param string $name
     */
    public function addNamespace($path, $name);

    /**
     * Adds a global variable that will be injected
     * in all views.
     *
     * @param string $name
     * @param string $value
     */
    public function addGlobal($name, $value);

    /**
     * Adds a function to the list of functions
     * available from the views.
     *
     * @param string $name
     * @param callable $closure
     */
    public function addFunction($name, callable $closure);

    /**
     * Adds a filter to the list of filters
     * available from the views.
     *
     * @param string $name
     * @param callable $closure
     */
    public function addFilter($name, callable $closure);
}