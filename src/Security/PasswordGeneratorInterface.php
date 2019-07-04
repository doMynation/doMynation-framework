<?php

namespace Domynation\Security;

/**
 * Interface PasswordGeneratorInterface
 *
 * @package Domynation\Security
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface PasswordGeneratorInterface
{

    /**
     * Generates a random password.
     *
     * @param array $data
     *
     * @return string
     */
    public function generate($data = []);
}