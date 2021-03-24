<?php

declare(strict_types=1);

namespace Domynation\Communication;

use Assert\Assertion;

/**
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class Email
{
    public function __construct(private string $email)
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