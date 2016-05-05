<?php

namespace Domynation\Exceptions;

interface ExceptionHandlerInterface
{

    /**
     * Handles the exception.
     *
     * @param \Exception $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(\Exception $e);
}