<?php

namespace Domynation\Core;

use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\RouterInterface;
use Domynation\View\ViewFactoryInterface;

/**
 * Interface ServiceProviderInterface
 *
 * @package Domynation\Core
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface ServiceProviderInterface
{

    /**
     * Starts the module.
     *
     * @param RouterInterface $router
     * @param \Domynation\View\ViewFactoryInterface $view
     * @param \Domynation\Eventing\EventDispatcherInterface $dispatcher
     */
    public function start(RouterInterface $router, ViewFactoryInterface $view, EventDispatcherInterface $dispatcher);

    /**
     * Registers the module's container definitions.
     *
     * @return array
     */
    public function registerContainerDefinitions();

    /**
     * Registers the module's routes.
     *
     * @param \Domynation\Http\RouterInterface $router
     *
     */
    public function registerRoutes(RouterInterface $router);

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