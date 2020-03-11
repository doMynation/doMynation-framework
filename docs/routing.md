# Modules

## What

Modules are the pillars of your app, and act as the central point of your app. Every application using the doMynation framework must have **at least one module**. A module is where you register the following 4 foundational components:

1. Dependencies
2. Routes
3. Templates
4. Event listeners

## Creating a Module

All module must extend the `Domynation\Core\Module` class and implement the following methods:

```php
final class MyModule extends Module
{
    public function registerContainerDefinitions(): array;
    
    public function registerRoutes(RouterInterface $router): void;
    
    public function registerViews(ViewFactoryInterface $view): void;
    
    public function registerListeners(EventDispatcherInterface $dispatcher): void;
}
```

### Registering Dependencies

The `registerContainerDefinitions` method is used to define and register your dependencies to be used via [dependency injection](https://en.wikipedia.org/wiki/Dependency_injection) throughout your app. Each definition is a key-value pair where the **key is a class name**, and the **value is a closure** that constructs and returns an instance of said class. 

**Note**: Each closure is container-aware, meaning you can inject previously-registered dependencies into them to facilitate the creation of complex objects.

```php
public function registerContainerDefinitions(): array
{
    return [
        MyDependencyA::class => function () {
            return new MyDependencyA(123, "Banana");
        },


        MyService::class => function (MyDependencyA $depA) {
            return new MyService($depA, "Tomato");
        },
        
        // ...
    ];
}
```

### Registering Routes

The `registerRoutes` method is used to define your routes. See the [Routing](routing-1.md) section for more details. 

```php
public function registerRoutes(RouterInterface $router): void
{
    $router->get('/', function () {
        return "Welcome home!";
    });
}

```

### Registering Templates

The `registerViews` method is where you configure your templates for a given module. See the Templating section for more details. 

```php
public function registerViews(ViewFactoryInterface $view): void
{
    $view->addNamespace('/modules/invoices', 'invoices');
    
    $view->addFunction('now', function () {
        return (new DateTime)->format("Y-m-d");
    });
}

```

### Registering Event Listeners

The `registerListeners` method is used to register listeners for particular events. See the Eventing section for more details.

```php
public function registerListeners(EventDispatcherInterface $dispatcher): void
{
    $dispatcher->listen(MyEvent::class, function (MyEvent $event) {
        // do something when this event is fired
    });
}

```

