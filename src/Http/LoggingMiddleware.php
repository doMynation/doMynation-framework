<?php

namespace Domynation\Http;

use Psr\Log\LoggerInterface;

final class LoggingMiddleware extends RouterMiddleware
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Route $route, array $inputs)
    {
        if (\User::isLogged()) {
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
                'id'   => \User::id(),
                'name' => \User::get('fullName')
            ],
            'route' => [
                'name'   => $route->getName(),
                'inputs' => $inputs
            ]
        ];

        $this->logger->info("Route: ", utf8_encode_array($data));
    }
}