<?php

namespace Domynation\Http\Middlewares;

use Assert\AssertionFailedException;
use Domynation\Exceptions\ValidationException;
use Domynation\Http\ResolvedRoute;
use Interop\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * A middleware responsible for calling the appropriate validator
 * for a request.
 *
 * @see Domynation\Validation\RouteValidator
 * @author Dominique Sarrazin <domynation@gmail.com>
 * @package Domynation\Http\Middlewares
 */
final class ValidationMiddleware extends RouteMiddleware
{

    /**
     * @var \Interop\Container\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResolvedRoute $resolvedRoute, Request $request)
    {
        $route = $resolvedRoute->getRoute();

        if ($route->hasValidator()) {
            // Instanciate the validator class
            $validator = $this->container->get($route->getValidator());

            try {
                if (method_exists($validator, 'validateRequest')) {
                    // If a `validateRequest` method exists on the validator, call it.
                    $validator->validateRequest($request);
                } else {
                    // Fall back to the traditional `validate` method.

                    $validator->validate($request->request->all());
                }
            } catch (AssertionFailedException $e) {
                throw new ValidationException([$e->getMessage()]);
            }
        }

        // Pass the request to the next middleware
        return !is_null($this->next) ? $this->next->handle($resolvedRoute, $request) : null;
    }
}