# Routing

## Introduction

Routes must be defined in your module's `registerRoutes()` method. The doMynation framework supports two types of routes:

1. Simple routes
2. Actions

Regardless of the type, all routes must be defined through an instance of `RouterInterface` , with one of the various methods available, each of which representing an [HTTP request method ](https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods):

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
    return new Response("Hello World!");
});
```

The doMynation frameworks uses Symfony's `Request` and `Response` classes for everything that's HTTP-related. Every route must return \(either explicitly, or implicitly via one of the [shorthands](routing-1.md#routing-shorthands)\) a Symfony `Response` object. 

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

Returning an `array` will automatically be converted to a `JsonResponse` object with the `200` status code and the appropriate response headers will be set for you.

```php
$router->get('/json', function () {
    return [
        'id'   => 123,
        'name' => 'Banana'
    ]; // equivalent of new JsonResponse([...])
});
```

#### Null Shorthand

Returning `null` \(or not returning anything\) will be converted to an empty `Response` object with a `200` status code.

```php
$router->delete('/comments', function () {
    // Code to delete all comments here
});
```



## Actions

## 

