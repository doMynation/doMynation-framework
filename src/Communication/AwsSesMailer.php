<?php

namespace Domynation\Communication;

use Aws\Ses\SesClient;

final class AwsSesMailer implements MailerInterface
{

    /**
     * @var \Aws\Ses\SesClient
     */
    private $client;

    /**
     * @var string
     */
    private $domain;


    /**
     * AwsSesMailer constructor.
     *
     * @param string $domain
     * @param string $region
     * @param string $apiKey
     * @param string $privateKey
     */
    public function __construct($domain, $region, $apiKey, $privateKey)
    {
        $this->domain = $domain;

        $this->client = new SesClient([
            'version'     => 'v3',
            'region'      => $region,
            'credentials' => [
                'key'    => $apiKey,
                'secret' => $privateKey
            ]
        ]);
    }

    /**
     * Sends an email.
     *
     * @param \Domynation\Communication\EmailMessage $email
     * @param array $data Additional data
     *
     * @return mixed
     */
    public function send(EmailMessage $email, $data = [])
    {
        $response = $this->client->sendEmail([
            'Source'      => $this->domain,
            'Destination' => [
                'ToAddresses' => $email->recipients
            ],
            'Message'     => [
                'Subject' => ['data' => $email->subject, 'charset' => 'iso-8895-1'],
                'Body'    => [
                    'Text' => ['data' => $email->body, 'charset' => 'iso-8895-1'],
                ]
            ]
        ]);
    }
}