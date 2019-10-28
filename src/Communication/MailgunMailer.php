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
     * MailgunMailer constructor.
     *
     * @param string $apiKey
     * @param string $domain
     */
    public function __construct(string $apiKey, string $domain)
    {
        $this->mailgun = new Mailgun($apiKey);
        $this->domain = $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function send(EmailMessage $message, $data = [])
    {
        $messageData = [
            'to'      => $message->recipients,
            'from'    => $message->getFullSender(),
            'subject' => $message->subject,
            'text'    => $message->body
        ];

        if (!empty($message->bcc)) {
            $messageData['bcc'] = $message->bcc;
        }

        // Set the mailgun domain
        $domain = !empty($data['domain']) ? $data['domain'] : $this->domain;

        // Send the message
        $this->mailgun->sendMessage($domain, $messageData);
    }
}