<?php

namespace Domynation\View;

use Twig_Environment;

/**
 * Class TwigViewFactory
 *
 * @package Domynation\View
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class TwigViewFactory implements ViewFactoryInterface
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $fileExtension;

    /**
     * TwigViewFactory constructor.
     *
     * @param \Twig_Environment $twig
     * @param string $defaultFileExtension
     */
    public function __construct(Twig_Environment $twig, string $defaultFileExtension)
    {
        $this->twig = $twig;

        // Always preprend a dot (.)
        $this->fileExtension = '.' . ltrim($defaultFileExtension, '.');
    }

    /**
     * {@inheritdoc}
     */
    public function render($viewName, $data = [])
    {
        $viewName = str_replace(".html.twig", "", $viewName);

        return $this->twig->render($viewName . $this->fileExtension, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function addNamespace($path, $name)
    {
        $this->twig->getLoader()->addPath($path, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function addGlobal($name, $value)
    {
        $this->twig->addGlobal($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function addFunction($name, callable $closure)
    {
        $this->twig->addFunction(new \Twig_SimpleFunction($name, $closure));
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter($name, callable $closure)
    {
        $this->twig->addFilter(new \Twig_SimpleFilter($name, $closure));
    }
}