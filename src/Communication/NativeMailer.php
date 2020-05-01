<?php

declare(strict_types=1);

namespace Domynation\Communication;

/**
 * Class NativeMailer
 *
 * @package Domynation\Communication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class NativeMailer implements MailerInterface
{
    private function getDefaultHeaders(): array
    {
        return [
            'X-Mailer: PHP' . PHP_VERSION
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function send(EmailMessage $email, $data = []): void
    {
        $headers = $this->getDefaultHeaders();
        $specialParams = '';

        if ($email->from) {
            $headers += [
                'Reply-To: ' . $email->from,
                'From: ' . $email->from
            ];

            $specialParams = '-f ' . $email->from;
        }

        $headersString = implode("\r\n", $headers);

        @mail($email->recipients, $email->subject, $email->body, $headersString, $specialParams);
    }
}