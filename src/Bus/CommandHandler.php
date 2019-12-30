<?php

namespace Domynation\Bus;

use Domynation\Eventing\EventDispatcherInterface;

/**
 * @package Domynation\Bus
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
abstract class CommandHandler
{

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Sets the event dispatcher for this command handler.
     *
     * @param mixed $dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handles the command.
     *
     * @param Command $command
     *
     * @return mixed
     */
    abstract public function handle(Command $command);
}