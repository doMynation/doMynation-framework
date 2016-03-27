<?php

namespace Domynation\Bus\Middlewares;

use Domynation\Bus\Command;
use Domynation\Bus\CommandHandler;
use Psr\Log\LoggerInterface;

final class LoggingMiddleware extends CommandBusMiddleware
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Command $command, CommandHandler $handler)
    {
        // Prepare the data
        $data = [];

        // Append the user info (if authentified)
        if (\User::isLogged()) {
            $data['user'] = [
                'id'   => \User::id(),
                'name' => utf8_encode(\User::get('fullName'))
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