# Modules

### Creating your first module

Each module must implement the following methods.

```php
final class MyModule extends Module
{

    public function registerContainerDefinitions(): array;
    public function registerRoutes(RouterInterface $router): void;
    public function registerViews(ViewFactoryInterface $view): void;
    public function registerListeners(EventDispatcherInterface $dispatcher): void;
    public function boot();
}
```



