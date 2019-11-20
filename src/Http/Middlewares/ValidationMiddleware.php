<?php

namespace Domynation\Http\Middlewares;

use Assert\AssertionFailedException;
use Domynation\Exceptions\ValidationException;
use Domynation\Http\ResolvedRoute;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use \Invoker\InvokerInterface;

/**
 * A middleware responsible for calling the appropriate validator for a request.
 *
 * @see Domynation\Validation\RouteValidator
 * @package Domynation\Http\Middlewares
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class ValidationMiddleware extends RouteMiddleware
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
     * ValidationMiddleware constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param \Invoker\InvokerInterface
     */
    public function __construct(ContainerInterface $container, InvokerInterface $invoker)
    {
        $this->container = $container;
        $this->invoker = $invoker;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResolvedRoute $resolvedRoute, Request $request)
    {
        $route = $resolvedRoute->getRoute();

        if ($route->hasValidator()) {
            try {
                $this->validate($request, $route->getValidator());
            } catch (AssertionFailedException $e) {
                throw new ValidationException([$e->getMessage()]);
            }
        }

        // Pass the request to the next middleware
        return $this->next !== null ? $this->next->handle($resolvedRoute, $request) : null;
    }

    /**
     * Validates the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $validator
     *
     * @throws \Invoker\Exception\InvocationException
     * @throws \Invoker\Exception\NotCallableException
     * @throws \Invoker\Exception\NotEnoughParametersException
     */
    private function validate(Request $request, $validator): void
    {
        if (is_callable($validator)) {
            // Call the closure and inject the dependencies
            $this->invoker->call($validator, [$request]);

            return;
        }

        // Instanciate the validator class and inject the dependencies
        $validator = $this->container->get($validator);

        if (method_exists($validator, 'validateRequest')) {
            // If a `validateRequest` method exists on the validator, call it.
            $validator->validateRequest($request);
        } else {
            // Fall back to the traditional `validate` method.
            $validator->validate($request->request->all());
        }
    }
}