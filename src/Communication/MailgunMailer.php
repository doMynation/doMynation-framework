<?php

namespace Domynation\Communication;

use Mailgun\Mailgun;

/**
 * @package Domynation\Communication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class MailgunMailer implements MailerInterface
{

    private $mailer;

    /**
     * @param string $apiKey
     * @param string $domain
     */
    public function __construct(string $apiKey, string $domain)
    {
        $transport = new \Symfony\Component\Mailer\Bridge\Mailgun\Transport\MailgunApiTransport($apiKey, $domain);
        $this->mailer = new \Symfony\Component\Mailer\Mailer($transport);
    }

    /**
     * {@inheritdoc}
     */
    public function send(EmailMessage $message, $data = [])
    {
        $email = (new \Symfony\Component\Mime\Email())
            ->from($message->getFullSender())
            ->to(...$message->recipients)
            ->subject($message->subject)
            ->text($message->body);

        if (!empty($message->bcc)) {
            $email->bcc($message->bcc);
        }

        $this->mailer->send($email);
    }
}