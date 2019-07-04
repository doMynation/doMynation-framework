<?php

namespace Domynation\Security;

/**
 * Class BasicPasswordGenerator
 *
 * @package Domynation\Security
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
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