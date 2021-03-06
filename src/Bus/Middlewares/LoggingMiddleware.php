<?php

namespace Domynation\Bus\Middlewares;

use Domynation\Authentication\UserInterface;
use Domynation\Bus\Command;
use Domynation\Bus\CommandHandler;
use Psr\Log\LoggerInterface;

/**
 * Command middleware that handles the logging of each
 * command passed to the bus.
 *
 * @package Domynation\Bus\Middlewares
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class LoggingMiddleware extends CommandBusMiddleware
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Domynation\Authentication\UserInterface
     */
    private $user;

    /**
     * LoggingMiddleware constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Domynation\Authentication\UserInterface $user
     */
    public function __construct(LoggerInterface $logger, UserInterface $user)
    {
        $this->logger = $logger;
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Command $command, CommandHandler $handler)
    {
        // Prepare the data
        $data = [];

        // Append the user info (if authentified)
        if ($this->user->isAuthenticated()) {
            $data['user'] = [
                'id'       => $this->user->getId(),
                'username' => $this->user->getUsername(),
            ];
        }

        // Serialize the command
        $data['command'] = $this->serialize($command);

        // Log the record
        $this->logger->addInfo("Command: ", $data);

        // Pass the request to the next handler
        return $this->next !== null ? $this->next->handle($command, $handler) : null;
    }

    /**
     * @param \Domynation\Bus\Command $command
     *
     * @return string
     */
    public function serialize(Command $command)
    {
        return serialize($command);
    }
}