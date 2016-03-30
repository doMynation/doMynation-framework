<?php

namespace Domynation\Http;

use Assert\AssertionFailedException;
use DI\Container;
use Domynation\Exceptions\ValidationException;

final class ValidationMiddleware extends RouterMiddleware
{

    /**
     * @var \Interop\Container\ContainerInterface
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Route $route, array $inputs)
    {
        if ($route->hasValidator()) {
            // Instanciate the validator class
            $validator = $this->container->get($route->getValidator());

            try {
                $validator->validate($inputs);
            } catch (AssertionFailedException $e) {
                throw new ValidationException([$e->getMessage()]);
            }
        }

        // Pass the request to the next middleware
        return !is_null($this->next) ? $this->next->handle($route, $inputs) : null;
    }
}