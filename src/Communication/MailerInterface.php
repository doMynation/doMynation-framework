<?php

declare(strict_types=1);

namespace Domynation\Communication;

/**
 * Interface MailerInterface
 *
 * @package Domynation\Communication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface MailerInterface
{
    /**
     * Sends an email.
     *
     * @param \Domynation\Communication\EmailMessage $email
     * @param array $data Additional data
     */
    public function send(EmailMessage $email, $data = []): void;
}