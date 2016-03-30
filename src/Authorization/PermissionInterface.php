<?php

namespace Domynation\Authorization;


interface PermissionInterface
{

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getCode();
}