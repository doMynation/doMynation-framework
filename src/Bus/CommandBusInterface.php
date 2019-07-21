<?php

namespace Domynation\Bus;

/**
 * Interface CommandBusInterface
 *
 * @package Domynation\Bus
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface CommandBusInterface
{
    /**
     * Executes the command.
     *
     * @param \Domynation\Bus\Command $command The command to execute
     *
     * @param bool $ignoreRaisedEvents Ignores all events raised within the command handler when set to true
     *
     * @return mixed
     */
    public function execute(Command $command, bool $ignoreRaisedEvents = false);
}