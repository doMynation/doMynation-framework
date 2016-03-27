<?php

namespace Domynation\Bus;

use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Utils\EventGeneratorTrait;

abstract class CommandHandler
{

    /**
     * @todo: Remove this and use the EventDispatcherInterface instead
     */
    use EventGeneratorTrait;

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
    abstract public function handle($command);
}