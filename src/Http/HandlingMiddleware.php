<?php

namespace Domynation\Http;

use Invoker\InvokerInterface;

/**
 * Class HandlingMiddleware
 *
 * This middleware has to be the last in the chain since it returns the final response.
 *
 * @package Domynation\Http
 */
final class HandlingMiddleware extends RouterMiddleware
{

    /**
     * @var \Invoker\InvokerInterface
     */
    private $invoker;

    /**
     * HandlingMiddleware constructor.
     *
     * @param \Invoker\InvokerInterface $invoker
     */
    public function __construct(\Invoker\InvokerInterface $invoker)
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