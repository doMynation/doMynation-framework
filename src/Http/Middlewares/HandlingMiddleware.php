<?php

namespace Domynation\Http\Middlewares;

use Domynation\Http\ResolvedRoute;
use Domynation\Session\SessionInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * This middleware has to be the last in the chain since it returns the final response.
 *
 * @package Domynation\Http\Middleware
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class HandlingMiddleware extends RouteMiddleware
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * @var \Invoker\InvokerInterface
     */
    private $invoker;

    /**
     * @var \Domynation\Session\SessionInterface
     */
    private $session;

    public function __construct(\Psr\Container\ContainerInterface $container, InvokerInterface $invoker, SessionInterface $session)
    {
        $this->container = $container;
        $this->invoker = $invoker;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResolvedRoute $resolvedRoute, Request $request)
    {
        $route = $resolvedRoute->getRoute();

        // Close the session for writes for routes that aren't going
        // to write anything to the session.
        if ($resolvedRoute->getRoute()->isReadOnly()) {
            $this->session->close();
        }

        if (is_callable($route->getHandler())) {
            // Call the route controller using the URI parameters AND inject
            // any dynamically defined dependencies through the container.
            return $this->invoker->call($route->getHandler(), $resolvedRoute->getParameters());
        }

        // The handler is an "Action"
        return $this->handleAction($route->getHandler(), $resolvedRoute);
    }

    private function handleAction(string $className, ResolvedRoute $resolvedRoute)
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Action {$className} not found for route {$resolvedRoute->getRoute()->getName()}.");
        }

        try {
            new \ReflectionMethod($className, 'run');
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException("Action {$className} is missing a `run` method.");
        }

        // Instantiate the class and inject all its dependencies
        $instance = $this->container->get($className);

        if ($this->usesBaseTrait($instance)) {
            $instance->setRequest($this->container->get(Request::class));
            $instance->setUser($this->container->get(\Domynation\Authentication\UserInterface::class));
            $instance->setView($this->container->get(\Domynation\View\ViewFactoryInterface::class));
            $instance->setBus($this->container->get(\Domynation\Bus\CommandBusInterface::class));
        }

        try {
            $method = new \ReflectionMethod($className, 'validate');

            // Check if the action has a non-static `validate` method and if so, call it
            if (!$method->isStatic()) {
                $this->validateAction($className, $resolvedRoute);
            }
        } catch (\ReflectionException $e) {
        }

        // Call the action's `run` method with the path parameters
        return call_user_func_array([$instance, 'run'], $resolvedRoute->getParameters());
    }

    private function validateAction($instance, ResolvedRoute $resolvedRoute)
    {
        try {
            return $this->invoker->call([$instance, 'validate'], $resolvedRoute->getParameters());
        } catch (\Assert\AssertionFailedException $e) {
            throw new \Domynation\Exceptions\ValidationException([$e->getMessage()]);
        }
    }

    /**
     * Returns true if the Action uses the `BaseActionTrait`.
     *
     * @param $instance
     *
     * @return bool
     */
    private function usesBaseTrait($instance): bool
    {
        $allTraits = [];

        do {
            $currentClassTraits = class_uses($instance);
            $allTraits = array_merge($allTraits, $currentClassTraits);

            if (in_array(\Domynation\Http\BaseActionTrait::class, $currentClassTraits, true)) {
                return true;
            }
        } while ($instance = get_parent_class($instance));

        foreach ($traits as $trait => $_) {
            $traitTraits = class_uses($trait);

            if (in_array(\Domynation\Http\BaseActionTrait::class, $traitTraits, true)) {
                return true;
            }
        }

        return false;
    }
}