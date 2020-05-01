<?php

declare(strict_types=1);

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
    public array $recipients;
    public string $subject;
    public string $body;
    public string $from;
    public ?string $fromName;
    public array $bcc;

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
    public function __construct(array $recipients, string $subject, string $body, string $from, ?string $fromName = null, array $bcc = [])
    {
        $transformedRecipients = is_array($recipients) ? $recipients : [$recipients];

        Assertion::email($from, 'Invalid FROM email');
        Assertion::allIsInstanceOf($transformedRecipients, Email::class, 'Invalid recipient(s)');

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
}