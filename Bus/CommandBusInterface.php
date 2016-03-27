<?php

namespace Domynation\Bus;

interface CommandBusInterface
{
    /**
     * Executes the command.
     *
     * @param \Domynation\Bus\Command $command
     *
     * @return mixed
     */
    public function execute(Command $command);
}