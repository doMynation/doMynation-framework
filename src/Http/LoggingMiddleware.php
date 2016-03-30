<?php

namespace Domynation\Http;

use Domynation\Authentication\UserInterface;
use Psr\Log\LoggerInterface;

final class LoggingMiddleware extends RouterMiddleware
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Domynation\Authentication\UserInterface
     */
    private $user;

    public function __construct(LoggerInterface $logger, UserInterface $user)
    {
        $this->logger = $logger;
        $this->user   = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Route $route, array $inputs)
    {
        if ($this->user->isAuthenticated()) {
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
        $data = [
            'user'  => [
                'id'   => $this->user->getId(),
                'name' => $this->user->getFullName(),
            ],
            'route' => [
                'name'   => $route->getName(),
                'inputs' => $inputs
            ]
        ];

        $this->logger->info("Route: ", utf8_encode_array($data));
    }
}