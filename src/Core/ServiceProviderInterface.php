<?php

namespace Domynation\Core;

use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\Router;
use Domynation\Http\RouterInterface;
use Domynation\View\ViewFactoryInterface;

interface ServiceProviderInterface
{

    /**
     * Starts the module.
     *
     * @todo: Remove the old router
     *
     * @param RouterInterface $router
     * @param \Domynation\View\ViewFactoryInterface $view
     * @param \Domynation\Eventing\EventDispatcherInterface $dispatcher
     * @param \Domynation\Http\Router $router2 (old router for backward compatibility)
     */
    public function start(RouterInterface $router, ViewFactoryInterface $view, EventDispatcherInterface $dispatcher, Router $router2);

    /**
     * Registers the module's container definitions.
     *
     * @return array
     */
    public function registerContainerDefinitions();

    /**
     * Registers the module's routes.
     *
     * @param \Domynation\Http\Router $oldRouter
     * @param \Domynation\Http\Router|\Domynation\Http\RouterInterface $router
     *
     */
    public function registerRoutes(RouterInterface $router, Router $oldRouter);

    /**
     * Registers the module's views.
     *
     * @param \Domynation\View\ViewFactoryInterface $view
     *
     * @return void
     */
    public function registerViews(ViewFactoryInterface $view);

    /**
     * Registers the module's event listeners.
     *
     * @param \Domynation\Eventing\EventDispatcherInterface $dispatcher
     *
     * @return void
     */
    public function registerListeners(EventDispatcherInterface $dispatcher);
}