<?php

namespace Domynation\Communication;

use Mailgun\Mailgun;

/**
 * Class MailgunMailer
 *
 * @package Domynation\Communication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
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

    /**
     * MailgunMailer constructor.
     *
     * @param string $apiKey
     * @param string $defaultDomain
     * @param string $defaultSender
     */
    public function __construct($apiKey, $defaultDomain, $defaultSender)
    {
        $this->mailgun       = new Mailgun($apiKey);
        $this->domain        = $defaultDomain;
        $this->defaultSender = $defaultSender;
    }

    /**
     * {@inheritdoc}
     */
    public function send(EmailMessage $message, $data = [])
    {
        $messageData = [
            'to'      => $message->recipients,
            'subject' => $message->subject,
            'text'    => $message->body
        ];

        // Set the mailgun domain
        $domain = !empty($data['domain']) ? $data['domain'] : $this->domain;

        if (!empty($message->bcc)) {
            $messageData['bcc'] = $message->bcc;
        }

        // Set the from
        $messageData['from'] = $message->hasSender() ? $message->getFullSender() : $this->defaultSender;

        // Send the message
        $this->mailgun->sendMessage($domain, $messageData);
    }
}