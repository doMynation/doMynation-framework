<?php

namespace Domynation\Communication;

/**
 * Class DebugMailer
 *
 * @package Domynation\Communication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class DebugMailer implements MailerInterface
{
    /**
     * @var \Domynation\Communication\MailerInterface
     */
    private $mailer;

    /**
     * @var string
     */
    private $destinationEmail;

    /**
     * DebugMailer constructor.
     *
     * @param \Domynation\Communication\MailerInterface $mailer
     * @param string $destinationEmail
     */
    public function __construct(MailerInterface $mailer, $destinationEmail)
    {
        $this->mailer           = $mailer;
        $this->destinationEmail = $destinationEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function send(EmailMessage $message, $data = [])
    {
        $recipient = function (Email $email) {
            return $email->getValue();
        };

        $emails = array_map(function (Email $email) {
            return $email->getValue();
        }, $message->recipients);
        $recipientsString = implode(', ', $emails);

        // Append the original recipients to the end of the email for debug purposes
        $message->body .= <<<EOT


------------- DEBUG INFO ----------------

Original recipients: {$recipientsString}

----------------------------------------------
EOT;

        // Alter the recipients to send them all to the dev mailbox
        $message->recipients = [$this->destinationEmail];

        // Send the email using the normal mailer
        $this->mailer->send($message, $data);
    }

}