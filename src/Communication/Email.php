<?php

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
    /**
     * @var string
     */
    private $email;

    /**
     * Email constructor.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        Assertion::email($email, "Invalid email address {$email}");

        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getValue() : string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function get(): string
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