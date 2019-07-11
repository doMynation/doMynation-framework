<?php

namespace Domynation\Communication;

use Assert\Assertion;

/**
 * Class EmailMessage
 *
 * @package Domynation\Communication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class EmailMessage
{

    public $recipients;
    public $subject;
    public $body;
    public $from;
    public $fromName;
    public $bcc;

    /**
     * EmailMessage constructor
     *
     * @param Email|Email[] $recipients
     * @param string $subject
     * @param string $body
     * @param string $from
     * @param string $fromName
     * @param array $bcc
     */
    public function __construct($recipients, string $subject, string $body = '', ?string $from = null, ?string $fromName = null, array $bcc = [])
    {
        $transformedRecipients = is_array($recipients) ? $recipients : [$recipients];

        Assertion::nullOrEmail($from, "Invalid FROM email");
        Assertion::allIsInstanceOf($transformedRecipients, Email::class, "Invalid recipient(s)");

        $this->recipients = $transformedRecipients;
        $this->subject = $subject;
        $this->body = $body;
        $this->from = $from;
        $this->fromName = $fromName;
        $this->bcc = $bcc;
    }

    /**
     * @return string
     */
    public function getFullSender(): string
    {
        $name = $this->fromName ? $this->fromName . ' ' : '';

        return $name . '<' . $this->from . '>';
    }

    /**
     * @return bool
     */
    public function hasSender(): bool
    {
        return $this->from !== null;
    }
}