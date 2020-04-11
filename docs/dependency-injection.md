# Dependency Injection

## Introduction

The doMynation uses [dependency injection](https://en.wikipedia.org/wiki/Dependency_injection) to wire dependencies across your app. 

### Container-Aware Entities

Below is the list of classes and methods supplied by the framework that are aware of the dependency injection container, which means they can be injected dependencies via constructor arguments or function/method arguments.

* The `Module::boot()` method
* Closures of [simple routes](routing-1.md#simple-routes)
* Constructor of [Actions](routing-1.md#actions)
* Route middlewares

### Core Dependencies

The framework registers and makes the following dependencies available in any container-aware entity:

| Class/Interface | Description |
| :--- | :--- |
| `RouterInterface` | The framework's router to register routes. |
| `Request` | The current request. |
| `ViewFactoryInterface` | The utility for loading templates/views and register namespaces. |
| `CommandBusInterface` | The command bus. |
| `UserInterface` | The currently logged-in user \(if any\). |
| `EntityManager` | Doctrine's ORM. |
| `CacheInterface` | The utility to store and retrieve data from the cache. |
| `StorageInterface` | The utility to store files. |
| `MailerInterface` | The utility to send emails. |
| `EventDispatcherInterface` | The event dispatcher, to fire and listen for events. |



