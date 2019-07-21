<?php

namespace Domynation\Communication;

use Aws\Ses\SesClient;

/**
 * Class AwsSesMailer
 *
 * @package Domynation\Communication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
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
            'version'     => 'latest',
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
            'Source'      => EMAIL_NO_REPLY,
            'Destination' => [
                'ToAddresses' => $email->recipients
            ],
            'Message'     => [
                'Subject' => ['Data' => $email->subject, 'charset' => 'latin1'],
                'Body'    => [
                    'Text' => ['Data' => $email->body, 'charset' => 'latin1'],
                ]
            ]
        ]);
    }
}