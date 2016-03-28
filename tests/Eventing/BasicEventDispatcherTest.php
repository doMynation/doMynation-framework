<?php

use Domynation\Eventing\BasicEventDispatcher;
use Domynation\Eventing\EventDispatcherInterface;
use Test\Eventing\TestEvent;

class BasicEventDispatcherTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var BasicEventDispatcher
     */
    protected $dispatcher;

    public function setUp()
    {
        $invoker = $this->getMockBuilder(\DI\InvokerInterface::class)->getMock();

        $invoker->method('call')->will($this->returnCallback(function ($value) {
            call_user_func_array($value, [new TestEvent("value")]);
        }));

        $this->dispatcher = new BasicEventDispatcher($invoker);
    }

    /**
     * @test
     */
    public function it_initiliazes()
    {
        $this->assertEquals(0, count($this->dispatcher->getListeners()));
        $this->assertEquals(0, count($this->dispatcher->getRaisedEvents()));
    }

    /**
     * @test
     */
    public function it_finds_all_listeners_for_a_specific_event()
    {
        $listener = [
            'name'     => TestEvent::class,
            'closure'  => function (TestEvent $event) {
            },
            'priority' => EventDispatcherInterface::PRIORITY_MEDIUM
        ];

        $this->dispatcher->listen(TestEvent::class, $listener['closure']);

        $listeners = $this->dispatcher->getListeners(TestEvent::class);

        $this->assertEquals(1, count($listeners));
        $this->assertSame($listener, $listeners[0]);
    }

    /**
     * @test
     */
    public function it_adds_an_event_listener()
    {
        $this->dispatcher->listen(TestEvent::class, function (TestEvent $event) {
            // Do some stuff
        });

        $listeners = $this->dispatcher->getListeners();

        $this->assertEquals(1, count($listeners));
    }

    /**
     * @test
     */
    public function it_adds_an_event_listener_with_a_priority()
    {
        $listener = [
            'name'     => TestEvent::class,
            'closure'  => function (TestEvent $event) {
            },
            'priority' => 500
        ];

        $this->dispatcher->listen(TestEvent::class, function (TestEvent $event) {

        }, 500);

        $listeners = $this->dispatcher->getListeners(TestEvent::class);

        $this->assertEquals(1, count($listeners));
        $this->assertEquals($listener, $listeners[0]);
    }

    /**
     * @test
     */
    public function it_raises_an_event()
    {
        $this->dispatcher->raise(new TestEvent("hello"));

        $raisedEvents = $this->dispatcher->getRaisedEvents();

        $this->assertEquals(1, count($raisedEvents));
        $this->assertInstanceOf(TestEvent::class, $raisedEvents[0]);
    }

    /**
     * @test
     */
    public function it_dispatches_events()
    {
        $called = false;

        $this->dispatcher->listen(TestEvent::class, function (TestEvent $event) use (&$called) {
            $called = true;
        });

        $this->dispatcher->raise(new TestEvent("hello"));
        $this->dispatcher->dispatch();

        $this->assertEquals(0, count($this->dispatcher->getRaisedEvents()));

        $this->assertTrue($called);
    }

    /**
     * @test
     */
    public function it_dispatches_the_events_according_to_priority()
    {
        $result = [];

        $this->dispatcher->listen(TestEvent::class, function (TestEvent $event) use (&$result) {
            $result[] = 'b';
        }, EventDispatcherInterface::PRIORITY_MEDIUM);

        $this->dispatcher->listen(TestEvent::class, function (TestEvent $event) use (&$result) {
            $result[] = 'c';
        }, EventDispatcherInterface::PRIORITY_HIGH);

        $this->dispatcher->listen(TestEvent::class, function (TestEvent $event) use (&$result) {
            $result[] = 'a';
        }, EventDispatcherInterface::PRIORITY_LOW);

        $this->dispatcher->raise(new TestEvent("hello"));
        $this->dispatcher->dispatch();

        $this->assertEquals(['a', 'b', 'c'], $result);
    }

    /**
     * @xtest
     */
    public function it_stops_the_propagation_of_events()
    {
        $result = [];

        $this->dispatcher->listen(TestEvent::class, function (TestEvent $event) use (&$result) {
            $result[] = 'a';
        });

        $this->dispatcher->listen(TestEvent::class, function (TestEvent $event) use (&$result) {
            $result[] = 'b';

            $event->stopPropagation();
        });

        // This shouldn't be called
        $this->dispatcher->listen(TestEvent::class, function (TestEvent $event) use (&$result) {
            $result[] = 'c';
        });

        $this->dispatcher->raise(new TestEvent('some data'));

        $this->dispatcher->dispatch();

        $this->assertEquals(['a', 'b'], $result);
    }
}
