<?php

declare(strict_types=1);

namespace Domynation\Communication;

use Mailgun\Mailgun;
use Symfony\Component\Mailer\Bridge\Mailgun\Transport\MailgunApiTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;

/**
 * @package Domynation\Communication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class MailgunMailer implements MailerInterface
{
    private Mailer $mailer;

    public function __construct(string $apiKey, string $domain)
    {
        $transport = new MailgunApiTransport($apiKey, $domain);
        $this->mailer = new Mailer($transport);
    }

    /**
     * {@inheritdoc}
     */
    public function send(EmailMessage $message, $data = []): void
    {
        $email = (new \Symfony\Component\Mime\Email())
            ->from(new Address($message->from, $message->fromName))
            ->to(...$message->recipients)
            ->subject($message->subject)
            ->text($message->body);

        if (!empty($message->bcc)) {
            $email->bcc($message->bcc);
        }

        $this->mailer->send($email);
    }
}