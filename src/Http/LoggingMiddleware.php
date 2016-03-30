<?php

namespace Domynation\Http;

use Domynation\Authentication\AuthenticatorInterface;
use Psr\Log\LoggerInterface;

final class LoggingMiddleware extends RouterMiddleware
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Domynation\Authentication\AuthenticatorInterface
     */
    private $auth;

    public function __construct(LoggerInterface $logger, AuthenticatorInterface $auth)
    {
        $this->logger = $logger;
        $this->auth = $auth;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Route $route, array $inputs)
    {
        if ($this->auth->isAuthenticated()) {
            $this->doLog($route, $inputs);
        }

        // Pass the request to the next middleware
        return !is_null($this->next) ? $this->next->handle($route, $inputs) : null;
    }

    /**
     * @param \Domynation\Http\Route $route
     * @param array $inputs
     */
    private function doLog(Route $route, array $inputs)
    {
        $user = $this->auth->getUser();

        $data = [
            'user'  => [
                'id'   => $user->getId(),
                'name' => $user->getFullName(),
            ],
            'route' => [
                'name'   => $route->getName(),
                'inputs' => $inputs
            ]
        ];

        $this->logger->info("Route: ", utf8_encode_array($data));
    }
}