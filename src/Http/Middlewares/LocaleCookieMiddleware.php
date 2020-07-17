<?php

declare(strict_types=1);

namespace Domynation\Http\Middlewares;

use Domynation\Config\ConfigInterface;
use Domynation\Http\Middlewares\RouteMiddleware;
use Domynation\Http\ResolvedRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Middleware that intercepts the final response and add cookies to it.
 *
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class LocaleCookieMiddleware extends RouteMiddleware
{
    private ConfigInterface $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResolvedRoute $resolvedRoute, Request $request)
    {
        /* @var $response \Symfony\Component\HttpFoundation\Response */
        $response = $this->next !== null ? $this->next->handle($resolvedRoute, $request) : null;

        // Store a cookie holding the preferred locale
        $i18nConfig = $this->config->get('i18n');
        $response->headers->setCookie(
            Cookie::create($i18nConfig['cookieName'])
                ->withValue($request->getLocale())
                ->withExpires((new \DateTimeImmutable)->modify("+{$i18nConfig['cookieLifetime']} day"))
                ->withSecure()
        );

        return $response;
    }
}