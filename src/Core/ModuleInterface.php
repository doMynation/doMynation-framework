<?php

declare(strict_types=1);

namespace Domynation\Core;

use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\RouterInterface;
use Domynation\View\ViewFactoryInterface;

/**
 * Interface ModuleInterface
 *
 * @package Domynation\Core
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface ModuleInterface
{
    /**
     * Starts the module.
     *
     * @param RouterInterface $router
     * @param \Domynation\View\ViewFactoryInterface $view
     * @param \Domynation\Eventing\EventDispatcherInterface $dispatcher
     */
    public function start(RouterInterface $router, ViewFactoryInterface $view, EventDispatcherInterface $dispatcher): void;

    /**
     * Registers the module's container definitions.
     *
     * @return array
     */
    public function registerContainerDefinitions(): array;

    /**
     * Registers the module's routes.
     *
     * @param \Domynation\Http\RouterInterface $router
     *
     */
    public function registerRoutes(RouterInterface $router): void;

    /**
     * Registers the module's views.
     *
     * @param \Domynation\View\ViewFactoryInterface $view
     */
    public function registerViews(ViewFactoryInterface $view): void;

    /**
     * Registers the module's event listeners.
     *
     * @param \Domynation\Eventing\EventDispatcherInterface $dispatcher
     */
    public function registerListeners(EventDispatcherInterface $dispatcher): void;
}