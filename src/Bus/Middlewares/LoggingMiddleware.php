<?php

namespace Domynation\Bus\Middlewares;

use Domynation\Authentication\AuthenticatorInterface;
use Domynation\Bus\Command;
use Domynation\Bus\CommandHandler;
use Psr\Log\LoggerInterface;

final class LoggingMiddleware extends CommandBusMiddleware
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

    public function handle(Command $command, CommandHandler $handler)
    {
        // Prepare the data
        $data = [];

        // Append the user info (if authentified)
        if ($this->auth->isAuthenticated()) {
            $user = $this->auth->getUser();

            $data['user'] = [
                'id'   => $user->getId(),
                'name' => utf8_encode($user->getFullName())
            ];
        }

        // Serialize the command
        $data['command'] = $this->serialize($command);

        // Log the record
        $this->logger->addInfo("Command: ", $data);

        // Pass the request to the next handler
        return !is_null($this->next) ? $this->next->handle($command, $handler) : null;
    }

    public function serialize(Command $command)
    {
        return utf8_encode(serialize($command));
    }
}