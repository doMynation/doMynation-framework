<?php

namespace Domynation\Security;

final class BasicPasswordGenerator implements PasswordGeneratorInterface
{

    /**
     * Generates a random password.
     *
     * @param array $data
     *
     * @return string
     */
    public function generate($data = [])
    {
        $passwords = [
            'jesuisnouveau',
            'bidonbidon',
            'password',
            'solar',
            'supermotdepasse',
            'bienvenue',
            'motdepasse',
            'sunroom',
            'polyvision'
        ];

        return $passwords[array_rand($passwords)];
    }
}