<?php

namespace Domynation\Bus;

use Domynation\Eventing\EventDispatcherInterface;
use Psr\Container\ContainerInterface;

/**
 * A basic implementation of a command bus.
 *
 * @package Domynation\Bus
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class BasicCommandBus implements CommandBusInterface
{

    /**
     * @var \Domynation\Bus\Middlewares\CommandBusMiddleware
     */
    private $middlewareChain;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * @var \Domynation\Eventing\EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(ContainerInterface $container, EventDispatcherInterface $dispatcher, array $middlewares)
    {
        $this->middlewareChain = $this->buildMiddlewareChain($middlewares);
        $this->container = $container;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Command $command, bool $ignoreEvents = false)
    {
        // Resolve the handler class
        $handler = $this->resolveHandlerClass($command);

        // Inject the event dispatcher
        $handler->setEventDispatcher($this->dispatcher);

        // Pass the command and its handler in the middleware chain
        $response = $this->middlewareChain->handle($command, $handler);

        // Clear the raised events if requested
        if ($ignoreEvents) {
            $this->dispatcher->clearEvents();
        }

        // Dispatch raised events
        $this->dispatcher->dispatch();

        return $response;
    }

    /**
     * Takes an array of middleware to builds the middleware chain.
     *
     * @param array $middlewares
     *
     * @return mixed|null
     */
    private function buildMiddlewareChain(array $middlewares = [])
    {
        if (empty($middlewares)) {
            return null;
        }

        // Fetch the first middleware
        $next = array_shift($middlewares);

        // Set its next middleware
        $next->setNext($this->buildMiddlewareChain($middlewares));

        return $next;
    }

    /**
     * Resolves an instance of the handler class
     * corresponding to $command.
     *
     * @param Command $command
     *
     * @return CommandHandler
     * @throws \Exception
     */
    private function resolveHandlerClass(Command $command)
    {
        $reflectionObject = new \ReflectionObject($command);
        $shortName = $reflectionObject->getShortName();
        $className = $reflectionObject->getNamespaceName() . '\\Handlers\\' . $shortName . 'Handler';

        if (!class_exists($className)) {
            throw new \Exception("Command handler {$className} not found.");
        }

        // Let the container resolve the instance and inject the required dependencies.
        return $this->container->get($className);
    }
}