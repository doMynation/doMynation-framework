<?php

namespace Domynation\Communication;

use Assert\Assertion;

final class Email
{
    /**
     * @var string
     */
    private $email;

    public function __construct($email)
    {
        Assertion::email($email, "Invalid email address {$email}");

        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->email;
    }
}