<?php

use Domynation\Communication\Email;

class EmailAddressTest extends PHPUnit_Framework_TestCase
{

    public function validInputs()
    {
        return [
            ['dom@email.com'],
            ['d.c@jesus.com'],
            ['12345353@some-domain.net'],
            ['ifejwogfejw@fgoeiwjge.com'],
            ['sar.dom@dom.com'],
        ];
    }

    public function invalidInputs()
    {
        return [
            [''],
            ['name@'],
            ['name @domain.com'],
            ['feoijfeojife.com'],
            ['feoijfeojife.com'],
            ['dom@dom@com.com']
        ];
    }

    /**
     * @test
     * @dataProvider validInputs
     */
    public function it_creates_a_valid_email_address($email)
    {
        $email = new Email($email);
    }

    /**
     * @test
     * @dataProvider invalidInputs
     * @expectedException InvalidArgumentException
     */
    public function it_fails_when_creating_an_invalid_email_address($email)
    {
        $email = new Email($email);
    }
}