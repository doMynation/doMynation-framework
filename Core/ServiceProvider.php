<?php

namespace Domynation\Core;

use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\Router;
use Domynation\View\ViewFactoryInterface;

/**
 * Class ServiceProvider
 *
 * This class is the entry point to a module. This is where all the necessary configuration and
 * initialization logic takes place to start a module.
 *
 * @package Domynation\Core
 */
abstract class ServiceProvider implements ServiceProviderInterface
{

    /**
     * Starts the module.
     *
     * @param \Domynation\Http\Router $router
     * @param \Domynation\View\ViewFactoryInterface $view
     * @param \Domynation\Eventing\EventDispatcherInterface $dispatcher
     */
    public function start(Router $router, ViewFactoryInterface $view, EventDispatcherInterface $dispatcher)
    {
        $this->registerRoutes($router);
        $this->registerViews($view);
        $this->registerListeners($dispatcher);
    }
}