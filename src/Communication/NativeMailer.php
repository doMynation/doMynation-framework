<?php

namespace Domynation\Communication;

/**
 * Class NativeMailer
 *
 * @package Domynation\Communication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class NativeMailer implements MailerInterface
{

    /**
     * @param array $headers
     *
     * @return array
     */
    private function getDefaultHeaders($headers = [])
    {
        return [
            "X-Mailer: PHP" . phpversion()
        ];
    }

    /**
     * {@inheritdoc}
     */
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