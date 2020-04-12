# Routing

## Introduction

Routes must be defined in your module's `registerRoutes()` method. The doMynation framework supports two types of routes:

1. Simple routes
2. Actions

Regardless of the type, all routes must be defined through an instance of `RouterInterface` , with one of the various methods available, each of which representing a [HTTP request method ](https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods):

* `get(string $path, $controller, string $name = null)`
* `post(string $path, $controller, string $name = null)`
* `patch(string $path, $controller, string $name = null)`
* `put(string $path, $controller, string $name = null)`
* `delete(string $path, $controller, string $name = null)`

The first argument is the route's URI \(e.g. `/invoices`\), the second argument is the controller to handle requests matching this route. Controllers can either be a closure \(see [Simple Routes](routing-1.md#simple-routes)\) or the fully-qualified name of a class \(see [Actions](routing-1.md#actions)\). The third and last argument is an optional name to give this route.

## Simple Routes

Simple routes are the easiest to implement and are more of a convenience than a recommended practice. A simple route uses a closure \([anonymous function](https://www.php.net/manual/en/functions.anonymous.php)\) as the handler.

### Your First Simple Route

Let's define a simple route that matches requests to `/hello` and outputs "Hello World!".

```php
use Symfony\Component\HttpFoundation\Response;

$router->get('/hello', function () {
    return new Response('Hello World!');
});
```

The doMynation frameworks uses Symfony's `Request` and `Response` classes for everything that's HTTP-related. Every route must return \(either explicitly, or implicitly via one of the [shorthands](routing-1.md#routing-shorthands)\) a Symfony `Response` object.

Since the framework uses Symfony's `Response` class, it means all response types from Symfony are supported. Visit Symfony's [documentation](https://symfony.com/doc/current/components/http_foundation.html#response) for more details.

### Routing Shorthands

In order to reduce boilerplate, the framework offers the following shorthands for route handlers:

#### String Shorthand

Returning a `string` will automatically be converted to a `Response` object with the `200` status code under the hood.

```php
$router->get('/hello', function () {
    return 'Hello World!'; // equivalent of new Response('Hello World!')
});
```

#### Array Shorthand

Returning an `array` will be converted to a `JsonResponse` object with the `200` status code and the appropriate response headers will be set for you.

```php
$router->get('/json', function () {
    return [
        'id'   => 123,
        'name' => 'Banana'
    ]; // equivalent of new JsonResponse([...])
});
```

#### Null Shorthand

Returning `null` \(or not returning anything\) will be converted to an empty `Response` object with a `200` status code. This is useful for write operations that don't need to return any data to the caller.

```php
$router->delete('/comments', function () {
    // Code to delete all comments here
});
```

### Path parameters

Path parameters \(also known as route parameters\) are useful to create routes with variable parts. They help you convey dynamic data through your URLs. You can use path parameters via the `{variableName}` syntax in your route's URI.

In the following example, visiting `/hello/Bob` will return `Hello Bob!` .

```php
$router->get('/hello/{name}', function (string $name) {
    return "Hello $name!";
});
```

You can have as many path parameters as you wish in a single route, they will always be **passed to your function in the** **order they are defined**. You can also add further requirements that the variables must meet via [regular expressions](https://en.wikipedia.org/wiki/Regular_expression) as such:

```php
$router->get('/invoices/{invoiceId<\d+>}', function (int $invoiceId) {
    return "Viewinig invoice $invoiceId.";
});
```

In the above example, only numeric invoice IDs will match the route, meaning calls such as `/invoices/banana` **will** **not match** this route. Calls such as `/invoices/8475` , on the other hand, **will match** the route.

### Accessing Dependencies

Closures in simple routes are [container-aware](dependency-injection.md#container-aware-entities). To use dependencies within your handler, simply list them as arguments to your closure and the framework will inject them for you.

```php
$router->get('/hello', function (Request $request, MyDependencyA $depA) {
    // use $deptA here ...

    return "The request's content type is {$request->getContentType()}.";
});
```

You can inject any dependency into your route handler as long as it is:

* One of the [core dependencies](dependency-injection.md#core-dependencies) registered by the framework.
* One of the [custom dependencies](routing.md#registering-dependencies) registered through your modules

You can have both path parameters and dependencies as arguments to your closure. The rules are as follow:

1. Path parameters are **always passed first** to your function
2. Dependencies are **always passed second**, after path parameters

Here's the previous example again, this time with a path parameter:

```php
$router->get('/hello/{name}', function (string $name, Request $request, MyDependencyA $depA) {
    // use $deptA here ...

    return "Hello $name, the request's content type is {$request->getContentType()}.";
});
```

## Actions

Actions are a one-to-one mapping between a request and a response. They're a more powerful version of simple routes in the sense that classes are more powerful abstractions than functions. 

Actions are defined the same way simple routes are, with the exception that the second argument is a **fully-qualified class name rather than a closure**.

### Your First Action

Let's re-implement [one of the above](routing-1.md#path-parameters) examples as an action. The first step is to define the route:

```php
$router->get('/hello/{name}', HelloAction::class);
```

The second \(and last\) step is to create a class for the action. The only requirement imposed by the framework is that your class implements a `run()` method. The `run()` method will be called by the framework with the [path parameters](routing-1.md#path-parameters) \(if any\).

```php
// HelloAction.php

final class HelloAction
{
    public function run(string $name): void
    {
        return "Hello $name!";
    }
}
```

In the example above, the `$name` argument is required since the route contains path parameters. For routes without path parameters, the method signature would simply be `run()` with no arguments.

### Accessing Dependencies

Unlike simple routes, dependencies for actions aren't confusingly mixed with path parameters. Instead, they can be accessed by **injecting them in the constructor**.

```php
final class HelloAction
{
    private Request $request;
    private MyDependencyA $depA;

    public function __construct(Request $request, MyDependencyA $depA)
    {
        $this->request = $request;
        $this->depA = $depA;
    }

    public function run(string $name): void
    {
        // use $this->deptA here ...
        return "The request's content type is {$this->request->getContentType()}.";
    }
}
```

This separation of path parameters and dependencies not only makes it easy to test by passing stubs/mocks as arguments, it also facilitates re-using the same instance to test different inputs.

## Simple Routes VS Actions

Whether you should use simple routes or actions depends mainly on the size of the project you're dealing with. If you're building a To-Do app, actions are likely overkill and simple routes are the way to go. However, if you're building the new SalesForce with hundreds of different endpoints and operations, actions are the better choice.

For larger projects, actions scale better than simple routes for many reasons:

1. They encapsulate each operation in its own independent, isolated and decoupled class, making it really easy to locate and maintain existing code. Adding new operations is also easier as you don't have to touch existing code, you simply define a new action \(class\) for each new operation.
2. Actions are easily testable. Since an action is but a class with its dependency injected in the constructor, writing integration/functional tests becomes really straightforward and less cumbersome as you only need to stub/mock the dependencies for this single operation.
3. Given that they're simply a mapping from a URI to a class name \(`string` -&gt; `string` \), route definitions for actions can be cached for a small performance boost. Simple route definitions on the hand, cannot be cached at this moment due to their reliance on closures \(`string` -&gt; `callable`\).

In essence, it boils down to **convenience vs scalability**. Simple routes are convenient due to their simplicity, whereas actions are more scalable to their encapsulation and testability.

### Why Not Controllers?

[Traditional controllers](https://symfony.com/doc/current/controller.html#a-simple-controller) handling more than one route **are fine** and there is absolutely nothing wrong with this approach. The reason behind the absence of them in the doMynation framework is mere [personal opinion](./#what). 

Having built larger applications in the past, I found that the traditional controller approach ultimately often results in **large controllers with little cohesion** that are difficult to maintain. They start small, but as the app grows in size, more and more endpoints/operations are added to existing controllers up to a point where it becomes difficult to locate and maintain existing code. I'm aware that controllers can be split into smaller ones when they become too big, but that just moves the problem and eventually the cycle repeats.

In its most basic form, a Web-based application is nothing but a **series of request handlers that each take a request and produce a response**. In other words, no matter what kind of operation you're dealing with \(e.g. viewing an invoice, deleting a comment, purchasing a product\), every operation can be seen as a function from a request to a response \(`request` -&gt; `response`\), and this is exactly what actions attempt to represent.

Please note that I am **not saying that actions are better than controllers**. Again this is simple personal preference. Both approaches have their advantage and inconvenient, and actions may very well be a lesser approach in many situations. For instance, actions might induce more boilerplate and more files in your project than a traditional controller approach would. 

All in all, nothing comes for free in software engineering, everything is a trade-off. 

