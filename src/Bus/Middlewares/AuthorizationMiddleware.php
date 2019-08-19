<?php

namespace Domynation\Bus\Middlewares;

use Domynation\Bus\Command;
use Domynation\Bus\CommandHandler;
use Domynation\Exceptions\AuthorizationException;

/**
 * Command middleware responsible for authorizing the execution.
 *
 * @package Domynation\Bus\Middlewares
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class AuthorizationMiddleware extends CommandBusMiddleware
{

    /**
     * Handles the command
     *
     * @param Command $command
     * @param CommandHandler $handler
     *
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle(Command $command, CommandHandler $handler)
    {
        // Authorize
        if (!$this->authorize($command, $handler)) {
            throw new AuthorizationException;
        }

        // Pass to the next middleware
        return $this->next !== null ? $this->next->handle($command, $handler) : null;
    }

    /**
     * Authorizes the execution of the command.
     *
     * @param Command $command
     * @param CommandHandler $handler
     *
     * @return bool
     */
    private function authorize(Command $command, CommandHandler $handler)
    {
        // If an "authorize" method is defined, execute it. Otherwise return true.
        return method_exists($handler, 'authorize') ? $handler->authorize($command) : true;
    }
}