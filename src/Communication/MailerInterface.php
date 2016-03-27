<?php

namespace Domynation\Communication;

interface MailerInterface
{

    /**
     * Sends an email
     *
     * @param \Domynation\Communication\EmailMessage $email
     * @param array $data Additional data
     *
     * @return mixed
     */
    public function send(EmailMessage $email, $data = []);
}