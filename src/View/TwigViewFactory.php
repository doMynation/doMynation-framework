<?php

declare(strict_types=1);

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
    private Twig_Environment $twig;

    /**
     * The path where view files are located.
     *
     * @var string
     */
    private string $path;
    private string $fileExtension;

    public function __construct(Twig_Environment $twig, string $path, string $defaultFileExtension)
    {
        $this->twig = $twig;
        $this->path = $path;
        $this->fileExtension = '.' . ltrim($defaultFileExtension, '.'); // Always preprend a dot (.)
    }

    /**
     * {@inheritdoc}
     */
    public function render($viewName, $data = []): string
    {
        $viewName = str_replace(".html.twig", "", $viewName);

        return $this->twig->render($viewName . $this->fileExtension, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function addNamespace($path, $name): void
    {
        $this->twig->getLoader()->addPath($this->path . $path, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function addGlobal($name, $value): void
    {
        $this->twig->addGlobal($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function addFunction($name, callable $closure): void
    {
        $this->twig->addFunction(new \Twig_SimpleFunction($name, $closure));
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter($name, callable $closure): void
    {
        $this->twig->addFilter(new \Twig_SimpleFilter($name, $closure));
    }
}