<?php

use DI\InvokerInterface;
use Domynation\Authentication\UserInterface;
use Domynation\Http\AuthenticationMiddleware;
use Domynation\Http\AuthorizationMiddleware;
use Domynation\Http\HandlingMiddleware;
use Domynation\Http\Router;
use Domynation\Http\ValidationMiddleware;
use Interop\Container\ContainerInterface;

class RouterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Router
     */
    protected $router;

    public function setUp()
    {
        $invoker = $this->getMock(InvokerInterface::class);
        $invoker->method('call')->willReturn('something');

        $container = $this->getMock(ContainerInterface::class);
        $container->method('get')->willReturn('something');

        $user = $this->getMock(UserInterface::class);

        $this->router = new Router(
            $container,
            new AuthenticationMiddleware($user),
            new AuthorizationMiddleware($user),
            new ValidationMiddleware($container),
            new HandlingMiddleware($invoker)
        );
    }

    /**
     * @test
     */
    public function it_builds_the_middleware_chain()
    {
        $middleware = $this->router->getMiddleware();

        $this->assertInstanceOf(AuthenticationMiddleware::class, $middleware);
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware->getNext());
        $this->assertInstanceOf(ValidationMiddleware::class, $middleware->getNext()->getNext());
        $this->assertInstanceOf(HandlingMiddleware::class, $middleware->getNext()->getNext()->getNext());
    }
}
