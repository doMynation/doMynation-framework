<?php

namespace Domynation\Core;

use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\Router;
use Domynation\View\ViewFactoryInterface;

interface ServiceProviderInterface
{

    /**
     * Starts the service provider.
     *
     * @param \Domynation\Http\Router $router
     * @param \Domynation\View\ViewFactoryInterface $view
     * @param \Domynation\Eventing\EventDispatcherInterface $dispatcher
     *
     * @return void
     */
    public function start(Router $router, ViewFactoryInterface $view, EventDispatcherInterface $dispatcher);

    /**
     * Registers the module's container definitions.
     *
     * @return array
     */
    public function registerContainerDefinitions();

    /**
     * Registers the module's routes.
     *
     * @param \Domynation\Http\Router $router
     *
     * @return void
     */
    public function registerRoutes(Router $router);

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