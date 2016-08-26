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
     * @param \Domynation\Bus\Command $command
     *
     * @return mixed
     */
    public function execute(Command $command);
}