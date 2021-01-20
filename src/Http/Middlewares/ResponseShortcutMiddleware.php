<?php

declare(strict_types=1);

namespace Domynation\Http\Middlewares;

use Domynation\Http\ResolvedRoute;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class ResponseShortcutMiddleware extends RouteMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function handle(ResolvedRoute $resolvedRoute, Request $request)
    {
        // Execute all other middlewares left
        $rawResponse = $this->next !== null ? $this->next->handle($resolvedRoute, $request) : null;

        return $this->parseResponse($request, $rawResponse);
    }

    /**
     * Parses the response using a series of helper functions to facilitate the
     * generation of a response from within the controller.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function parseResponse(Request $request, $response): Response
    {
        // Convert a redirect to a regular response for ajax requests
        if ($response instanceof RedirectResponse && $request->isXmlHttpRequest()) {
            $newResponse = new Response('', $response->getStatusCode());
            $newResponse->headers->set('Location', $response->getTargetUrl());

            return $newResponse;
        }

        if ($response instanceof Response) {
            return $response;
        }

        // Convert string to a simple response
        if (is_string($response)) {
            return new Response($response);
        }

        // Convert array to a Json response
        if (is_array($response) || $response instanceof \JsonSerializable) {
            return new JsonResponse($response);
        }

        // Convert everything else to an empty response
        return $request->isXmlHttpRequest()
            ? new JsonResponse
            : new Response;
    }
}