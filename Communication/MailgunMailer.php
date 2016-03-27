<?php

namespace Domynation\Communication;

use Mailgun\Mailgun;

final class MailgunMailer implements MailerInterface
{
    /**
     * @var \Mailgun\Mailgun
     */
    private $mailgun;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $defaultSender;

    public function __construct($apiKey, $defaultDomain, $defaultSender)
    {
        $this->mailgun       = new Mailgun($apiKey);
        $this->domain        = $defaultDomain;
        $this->defaultSender = $defaultSender;
    }

    public function send(EmailMessage $message, $data = [])
    {
        $messageData = [
            'to'      => $message->recipients,
            'subject' => utf8_encode($message->subject),
            'text'    => utf8_encode($message->body)
        ];

        // Set the mailgun domain
        $domain = !empty($data['domain']) ? $data['domain'] : $this->domain;

        if (!empty($message->bcc)) {
            $messageData['bcc'] = $message->bcc;
        }

        // Set the from
        $messageData['from'] = $message->hasSender() ? utf8_encode($message->getFullSender()) : $this->defaultSender;

        // Send the message
        $this->mailgun->sendMessage($domain, $messageData);
    }
}