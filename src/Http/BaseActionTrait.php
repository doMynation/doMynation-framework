<?php

declare(strict_types=1);

namespace Domynation\Http;

use Domynation\Authentication\UserInterface;
use Domynation\Eventing\EventDispatcherInterface;
use Domynation\I18N\Translator;
use Domynation\View\ViewFactoryInterface;
use JsonException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use \Domynation\Bus\CommandBusInterface;

/**
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
trait BaseActionTrait
{
    protected Request $request;
    protected UserInterface $user;
    protected CommandBusInterface $bus;
    protected ViewFactoryInterface $view;
    protected EventDispatcherInterface $dispatcher;
    protected Translator $translator;

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

    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * Returns the requested content type via a URI suffix. Falls back to 'text/html' if none is provided.
     *
     * e.g. '/customers/{customerId}.json' -> 'application/json'
     *
     * @return string
     */
    public function getRequestedContentType(): string
    {
        $contentType = pathinfo($this->request->getPathInfo(), PATHINFO_EXTENSION);

        switch ($contentType) {
            case 'json':
                return 'application/json';
            case 'xml':
                return 'application/xml';
            default:
                return 'text/html';
        }
    }

    /**
     * Parses a JSON request and return an array with the data.
     *
     * @return array
     */
    public function getJson(): array
    {
        if ($this->request->getContentType() !== 'json') {
            throw new RuntimeException("Request is not of type application/json");
        }

        try {
            return json_decode($this->request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException("Error while decoding JSON payload.");
        }
    }
}