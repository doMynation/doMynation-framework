<?php

namespace Domynation\Http;

use DI\InvokerInterface;

/**
 * Class HandlingMiddleware
 *
 * This middleware has to be the last in the chain since it returns the final responsen.
 *
 * @package Domynation\Http
 */
final class HandlingMiddleware extends RouterMiddleware
{

    /**
     * @var \Interop\Container\ContainerInterface
     */
    private $invoker;

    public function __construct(InvokerInterface $invoker)
    {
        $this->invoker = $invoker;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Route $route, array $inputs)
    {
        return $this->invoker->call($route->getHandler(), [$inputs]);
    }
}