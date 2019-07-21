<?php

namespace Domynation\Authorization;

/**
 * Interface PermissionInterface
 *
 * @package Domynation\Authorization
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
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