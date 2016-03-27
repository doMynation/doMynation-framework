<?php

namespace Domynation\Communication;

use Assert\Assertion;

final class EmailMessage
{

    public $recipients;
    public $subject;
    public $body;
    public $from;
    public $fromName;
    public $bcc;

    /**
     * @param Email|Email[] $recipients
     * @param string $subject
     * @param string $body
     * @param string $from
     * @param string $fromName
     * @param array $bcc
     */
    public function __construct($recipients, $subject, $body = '', $from = null, $fromName = null, $bcc = [])
    {
        $transformedRecipients = is_array($recipients) ? $recipients : [$recipients];

        Assertion::nullOrEmail($from, "Invalid FROM email");
        Assertion::allIsInstanceOf($transformedRecipients, 'Solarius\Common\ValueObjects\Email', "Invalid recipient(s)");

        $this->recipients = $transformedRecipients;
        $this->subject    = $subject;
        $this->body       = $body;
        $this->from       = $from;
        $this->fromName   = $fromName;
        $this->bcc        = $bcc;
    }

    /**
     * @return string
     */
    public function getFullSender()
    {
        $name = $this->fromName ? $this->fromName . ' ' : '';

        return $name . '<' . $this->from . '>';
    }

    /**
     * @return bool
     */
    public function hasSender()
    {
        return !is_null($this->from);
    }
}