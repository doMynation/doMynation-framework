<?php

namespace Domynation\Logging;

interface ApplicationLoggerInterface
{
    public function logRoute($data);
    public function logCommand($data);
    public function logErro($data);
}