<?php

namespace Domynation\Communication;

final class NativeMailer implements MailerInterface
{

    private function getDefaultHeaders($headers = [])
    {
        return [
            "X-Mailer: PHP" . phpversion()
        ];
    }

    public function send(EmailMessage $email, $data = [])
    {
        $headers = $this->getDefaultHeaders();

        $specialParams = '';

        if ($email->from) {
            $headers += [
                "Reply-To: " . $email->from,
                "From: " . $email->from
            ];

            $specialParams = '-f ' . $email->from;
        }

        $headersString = implode("\r\n", $headers);

        return @mail($email->recipients, $email->subject, $email->body, $headersString, $specialParams);
    }
}