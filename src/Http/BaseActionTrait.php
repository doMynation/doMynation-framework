<?php

namespace Domynation\Http;

use Domynation\Authentication\UserInterface;
use Domynation\View\ViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use \Domynation\Bus\CommandBusInterface;

/**
 * @package Domynation\Http
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
trait BaseActionTrait
{
    protected Request $request;
    protected UserInterface $user;
    protected CommandBusInterface $bus;
    protected ViewFactoryInterface $view;

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function setBus(CommandBusInterface $bus): void
    {
        $this->bus = $bus;
    }

    public function setView(ViewFactoryInterface $view): void
    {
        $this->view = $view;
    }
}