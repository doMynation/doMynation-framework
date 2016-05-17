<?php

namespace Domynation\Core;

use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\Router;
use Domynation\Http\RouterInterface;
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
     * {@inheritdoc}
     */
    public function start(RouterInterface $router, ViewFactoryInterface $view, EventDispatcherInterface $dispatcher, Router $oldRouter)
    {
        $this->registerRoutes($router, $oldRouter);
        $this->registerViews($view);
        $this->registerListeners($dispatcher);
    }
}