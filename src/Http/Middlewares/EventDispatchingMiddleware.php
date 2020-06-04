<?php

declare(strict_types=1);

namespace Domynation\Http\Middlewares;

use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\ResolvedRoute;
use Domynation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

final class EventDispatchingMiddleware extends RouteMiddleware
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResolvedRoute $resolvedRoute, Request $request)
    {
        // Execute all other middlewares left
        $response = $this->next !== null ? $this->next->handle($resolvedRoute, $request) : null;

        // Dispatch accumulated events
        $this->dispatcher->dispatch();

        return $response;
    }
}
