<?php

namespace Domynation\Bus\Middlewares;

use Domynation\Bus\Command;
use Domynation\Bus\CommandHandler;

/**
 * Class CommandBusMiddleware
 *
 * @package Domynation\Bus\Middlewares
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
abstract class CommandBusMiddleware
{
    /**
     * The next middleware in the chain.
     *
     * @var CommandBusMiddleware
     */
    protected $next;

    /**
     * Handles the command.
     *
     * @param Command $command
     * @param CommandHandler $handler
     *
     * @return mixed
     */
    abstract public function handle(Command $command, CommandHandler $handler);

    /**
     * Sets the next middleware to handle the command
     *
     * @param CommandBusMiddleware|null $middleware
     */
    public function setNext(CommandBusMiddleware $middleware = null)
    {
        $this->next = $middleware;
    }
}
