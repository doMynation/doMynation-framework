<?php

namespace Domynation\Security;

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