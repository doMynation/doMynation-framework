<?php

namespace Domynation\Bus\Middlewares;

use Domynation\Bus\Command;
use Domynation\Bus\CommandHandler;
use Domynation\Exceptions\AuthorizationException;

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
        return !is_null($this->next) ? $this->next->handle($command, $handler) : null;
    }

    /**
     * Authorizes the execution of the command
     *
     * @param Command $command
     * @param CommandHandler $handler
     *
     * @return bool
     */
    private function authorize(Command $command, CommandHandler $handler)
    {
        $authorized = true;

        // If the command has a "requiredPermissions" property,
        // use them to authorize the execution
        if (property_exists($handler, 'requiredPermissions')) {
            $authorized = \User::hasAccess($handler->requiredPermissions);
        }

        // If an "authorize" method is defined, execute it
        if ($authorized && method_exists($handler, 'authorize')) {
            $authorized = $handler->authorize($command);
        }

        return $authorized;
    }
}