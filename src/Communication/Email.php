<?php

declare(strict_types=1);

namespace Domynation\Communication;

use Assert\Assertion;

/**
 * Class Email
 *
 * @package Domynation\Communication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class Email
{
    private string $email;

    public function __construct(string $email)
    {
        Assertion::email($email, "Invalid email address {$email}");

        $this->email = $email;
    }

    public function getValue(): string
    {
        return $this->email;
    }

    public function get(): string
    {
        return $this->email;
    }

    public function __toString()
    {
        return $this->email;
    }
}