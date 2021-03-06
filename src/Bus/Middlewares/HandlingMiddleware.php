<?php

namespace Domynation\Bus\Middlewares;

use Domynation\Bus\Command;
use Domynation\Bus\CommandHandler;

/**
 * This middleware has to be the last in the chain since it returns the final response.
 *
 * @package Domynation\Bus\Middlewares
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class HandlingMiddleware extends CommandBusMiddleware
{

    /**
     * Handles the command and returns the response.
     *
     * @param \Domynation\Bus\Command $command
     * @param \Domynation\Bus\CommandHandler $handler
     *
     * @return mixed
     */
    public function handle(Command $command, CommandHandler $handler)
    {
        $response = $handler->handle($command);

        return $response;
    }
}