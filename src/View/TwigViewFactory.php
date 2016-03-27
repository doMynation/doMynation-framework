<?php

namespace Domynation\View;

use Twig_Environment;

final class TwigViewFactory implements ViewFactoryInterface
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render($viewName, $data = [])
    {
        return $this->twig->render($viewName, $data);
    }

    public function addNamespace($path, $name)
    {
        $this->twig->getLoader()->addPath($path, $name);
    }

    /**
     * Adds a global variable that will be injected
     * in all views.
     *
     * @param string $name
     * @param string $value
     */
    public function addGlobal($name, $value)
    {
        $this->twig->addGlobal($name, $value);
    }

    /**
     * Adds a function to the list of functions
     * available from the views.
     *
     * @param string $name
     * @param callable $closure
     */
    public function addFunction($name, callable $closure)
    {
        $this->twig->addFunction(new \Twig_SimpleFunction($name, $closure));
    }

    /**
     * Adds a filter to the list of functions
     * available from the views.
     *
     * @param string $name
     * @param callable $closure
     */
    public function addFilter($name, callable $closure)
    {
        $this->twig->addFilter(new \Twig_SimpleFilter($name, $closure));
    }
}