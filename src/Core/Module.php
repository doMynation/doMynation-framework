<?php

namespace Domynation\Core;

use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\RouterInterface;
use Domynation\View\ViewFactoryInterface;

/**
 * This class is the entry point to a module. This is where all the necessary configuration and
 * initialization logic takes place to boot a module.
 *
 * @package Domynation\Core
 */
abstract class Module implements ModuleInterface
{

    /**
     * {@inheritdoc}
     */
    public function start(RouterInterface $router, ViewFactoryInterface $view, EventDispatcherInterface $dispatcher)
    {
        $this->registerRoutes($router);
        $this->registerViews($view);
        $this->registerListeners($dispatcher);
    }
}