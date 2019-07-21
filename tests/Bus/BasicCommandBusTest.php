<?php

use Domynation\Bus\Command;
use Domynation\Bus\Middlewares\CommandBusMiddleware;
use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Bus\BasicCommandBus;
use Psr\Container\ContainerInterface;

class BasicCommandBusTest extends PHPUnit_Framework_TestCase
{
    protected $bus;

    public function setUp()
    {
        $container  = $this->getMock(ContainerInterface::class);
        $middleware = $this->getMock(CommandBusMiddleware::class);
        $middleware->method('setNext')->willReturn('nothing');

        $dispatcher = $this->getMock(EventDispatcherInterface::class);
        $this->bus  = new BasicCommandBus($container, $dispatcher, [$middleware]);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_should_throw_an_exception_when_a_corresponding_handler_class_doesnt_exist()
    {
        $command = $this->getMock(Command::class);

        $this->bus->execute($command);
    }

    /**
     * @xtest
     */
    public function it_should_call_the_handle_method_on_the_appropriate_handler_class()
    {

    }
}