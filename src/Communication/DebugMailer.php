<?php

namespace Domynation\Communication;

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

    public function __construct(MailerInterface $mailer, $destinationEmail)
    {
        $this->mailer           = $mailer;
        $this->destinationEmail = $destinationEmail;
    }

    public function send(EmailMessage $message, $data = [])
    {
        $recipient = function (Email $email) {
            return $email->getValue();
        };

        $commaSeparated   = curry('implode', ', ');
        $formatRecipients = compose($commaSeparated, map($recipient));
        $recipientsString = $formatRecipients($message->recipients);


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