<?php

declare(strict_types=1);

namespace Domynation\Http\Middlewares;

use Assert\AssertionFailedException;
use Domynation\Authentication\UserInterface;
use Domynation\Bus\CommandBusInterface;
use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Exceptions\ValidationException;
use Domynation\Http\BaseActionTrait;
use Domynation\Http\ResolvedRoute;
use Domynation\I18N\Translator;
use Domynation\Session\SessionInterface;
use Domynation\View\ViewFactoryInterface;
use InvalidArgumentException;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Request;

/**
 * This middleware has to be the last in the chain since it returns the final response.
 *
 * @package Domynation\Http\Middleware
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class HandlingMiddleware extends RouteMiddleware
{
    private ContainerInterface $container;
    private InvokerInterface $invoker;
    private SessionInterface $session;

    public function __construct(ContainerInterface $container, InvokerInterface $invoker, SessionInterface $session)
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
        // to write anything to the session. This may significantly improve performances in situations
        // where multiple AJAX requests are running in parallel.
        if ($resolvedRoute->getRoute()->isReadOnly()) {
            $this->session->close();
        }

        if (is_callable($route->getHandler())) {
            // Call the route controller using the URI parameters AND inject
            // any dynamically defined dependencies through the container.
            return $this->invoker->call($route->getHandler(), $resolvedRoute->getParameters());
        }

        // The handler is an "Action"
        return $this->handleAction($route->getHandler(), $resolvedRoute, $request);
    }

    /**
     * Handles an Action type of handler.
     *
     * @param string $className
     * @param \Domynation\Http\ResolvedRoute $resolvedRoute
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     * @throws \Domynation\Exceptions\ValidationException
     */
    private function handleAction(string $className, ResolvedRoute $resolvedRoute, Request $request)
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("Action {$className} not found for route {$resolvedRoute->getRoute()->getName()}.");
        }

        // Ensure the action has a `run` method.
        try {
            new ReflectionMethod($className, 'run');
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException("Action {$className} is missing a `run` method.");
        }

        // Instantiate the class and inject all its dependencies
        $instance = $this->container->get($className);

        // Automagically inject the most frequently used dependencies when the action
        // is using the `BaseActionTrait`. Setter injection is undoubtedly bad in most cases as it hinders testability, but
        // the amount of boilerplate code we're saving in all Actions makes it worth it.
        if ($this->usesBaseTrait($instance)) {
            $instance->setRequest($request);
            $instance->setUser($this->container->get(UserInterface::class));
            $instance->setView($this->container->get(ViewFactoryInterface::class));
            $instance->setBus($this->container->get(CommandBusInterface::class));
            $instance->setEventDispatcher($this->container->get(EventDispatcherInterface::class));
            $instance->setTranslator($this->container->get(Translator::class));
        }

        try {
            // Check if the action has a non-static `validate` method and if so, call it
            if (!(new ReflectionMethod($className, 'validate'))->isStatic()) {
                $this->validateAction($className, $resolvedRoute);
            }
        } catch (ReflectionException $e) {
        }

        // Call the action's `run` method with the path parameters
        return call_user_func_array([$instance, 'run'], $resolvedRoute->getParameters());
    }

    /**
     * Calls the `validate` method on an action and handles validation errors thrown
     * from the method.
     *
     * @param $instance
     * @param \Domynation\Http\ResolvedRoute $resolvedRoute
     *
     * @return mixed
     * @throws \Domynation\Exceptions\ValidationException
     */
    private function validateAction($instance, ResolvedRoute $resolvedRoute)
    {
        try {
            return $this->invoker->call([$instance, 'validate'], $resolvedRoute->getParameters());
        } catch (AssertionFailedException $e) {
            throw new ValidationException([$e->getMessage()]);
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

        // Loop through all the instance's trait (and their parent classes' traits)
        do {
            $currentClassTraits = class_uses($instance);
            $allTraits = array_merge($allTraits, $currentClassTraits);

            if (in_array(BaseActionTrait::class, $currentClassTraits, true)) {
                return true;
            }
        } while ($instance = get_parent_class($instance));

        // Loop through each trait found and their parent traits' traits
        foreach ($allTraits as $trait => $_) {
            $traitTraits = class_uses($trait);

            if (in_array(BaseActionTrait::class, $traitTraits, true)) {
                return true;
            }
        }

        return false;
    }
}