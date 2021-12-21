<?php

declare(strict_types=1);

namespace Domynation;

use DI\ContainerBuilder;
use Domynation\Config\ConfigInterface;
use Domynation\Config\InMemoryConfigStore;
use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\RouterInterface;
use Domynation\Session\SessionInterface;
use Domynation\Session\Session;
use Domynation\View\ViewFactoryInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * The framework kernel.
 *
 * @package Domynation
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class Application
{
    /**
     * The root path of the application.
     *
     * @var string
     */
    private string $basePath;

    /**
     * The environment the kernel is operating in.
     * Possible values: web, test
     *
     * @var string
     */
    private string $environment;

    /**
     * The dependency injection container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * The current HTTP request.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private ?Request $request;

    /**
     * Application constructor.
     *
     * @param string $basePath
     * @param string $environment
     */
    public function __construct(string $basePath, string $environment = 'web')
    {
        $this->basePath = realpath($basePath);
        $this->environment = $environment;
    }

    /**
     * Boots the kernel.
     *
     * @return void
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ReflectionException
     */
    public function boot(): void
    {
        // Boot the configuration
        $config = $this->bootConfiguration();

        // Boot the error reporting
        $this->bootErrorReporting($config);

        // Boot the request
        $this->request = $this->bootRequest($config);

        // Boot the session
        $session = $this->bootSession();

        // Boot the container
        $this->container = $this->bootContainer($config, $session);
    }

    /**
     * Boots the dependency injection container.
     *
     * @param \Domynation\Config\ConfigInterface $config
     * @param \Domynation\Session\SessionInterface $session
     *
     * @return \Psr\Container\ContainerInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ReflectionException
     */
    private function bootContainer(ConfigInterface $config, SessionInterface $session): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->useAnnotations(false);

        // Cache the definitions in production
        if (!$config->get('isDevMode')) {
            $builder->enableCompilation($this->basePath . '/cache');
        }

        // Load all global services
        $builder->addDefinitions(array_merge(require __DIR__ . '/Files/container.php', [
            ConfigInterface::class  => $config,
            Request::class          => $this->request,
            SessionInterface::class => $session
        ]));

        // First pass: Instanciate each module and load their container definitions
        $modules = array_reduce($config->get('modules'), function (array $accumulator, string $name) use ($builder) {
            if (!class_exists($name)) {
                return $accumulator;
            }

            // Instanciate the module
            $reflection = new ReflectionClass($name);
            $module = $reflection->newInstanceArgs();

            // Load the module's container definitions
            $builder->addDefinitions($module->registerContainerDefinitions());

            $accumulator[] = $module;

            return $accumulator;
        }, []);

        // Build the container
        $container = $builder->build();

        // Second pass: Boot each module
        foreach ($modules as $module) {
            // Start the module
            $module->start(
                $container->get(RouterInterface::class),
                $container->get(ViewFactoryInterface::class),
                $container->get(EventDispatcherInterface::class)
            );

            // Use reflection to get a closure copy of the `boot` method
            $reflection = new ReflectionClass(get_class($module));
            $closure = $reflection->getMethod('boot')->getClosure($module);

            // Let the container inject the dependency needed by the closure
            $container->call($closure);
        }

        return $container;
    }

    /**
     * Boots the session.
     *
     * @return \Domynation\Session\SessionInterface
     */
    private function bootSession(): SessionInterface
    {
        // Use PHP's native session for the web, and an in-memory handler for unit tests.
        $session = $this->environment === 'web'
            ? new Session(new NativeSessionStorage([], new NativeFileSessionHandler()))
            : new Session(new MockArraySessionStorage());

        $session->start();

        return $session;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Boots the configuration store.
     *
     * @return \Domynation\Config\ConfigInterface
     */
    private function bootConfiguration(): ConfigInterface
    {
        // Set the default timezone
        date_default_timezone_set('UTC');

        // Load application configurations
        $applicationConfig = require $this->basePath . '/config/application.php';
        $applicationConfig['basePath'] = $this->basePath;
        $applicationConfig['environment'] = $this->environment;

        return new InMemoryConfigStore($applicationConfig);
    }

    /**
     * Executes the request and returns the response.
     *
     * @return mixed
     * @throws \Domynation\Http\RouteNotFoundException
     */
    public function run(): void
    {
        $router = $this->container->get(RouterInterface::class);

        // Resolve the controller and arguments for the requested route
        $response = $router->handle($this->request);

        // Send the response
        $response->send();
    }

    /**
     * Boots error reporting.
     *
     * @param \Domynation\Config\ConfigInterface $config
     *
     * @return void
     */
    private function bootErrorReporting(ConfigInterface $config): void
    {
        // Report all errors
        error_reporting(E_ALL ^ E_DEPRECATED);

        $isDevMode = $config->get('isDevMode');
        $debugHandler = $isDevMode ? new PrettyPageHandler : function () {
            echo "Whoops, something went wrong...";
        };

        $whoops = new Run();
        $whoops->pushHandler($debugHandler);
        $whoops->register();
    }

    /**
     * Boots the request.
     *
     * @param \Domynation\Config\ConfigInterface $config
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function bootRequest(ConfigInterface $config): Request
    {
        $i18nConfig = $config->get('i18n');

        // Create the request object and guess the best locale
        $request = Request::createFromGlobals();
        $guessedLocale = $this->guessLocale($request, $i18nConfig['cookieName'], $i18nConfig['supportedLocales']);

        // Attach the locale to the request object
        $request->setLocale(
            in_array($guessedLocale, $i18nConfig['supportedLocales'], true)
                ? $guessedLocale
                : $i18nConfig['fallbackLocale']
        );

        return $request;
    }

    /**
     * Guesses the locale based on the environment.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $localeCookieName
     * @param array $supportedLocales
     *
     * @return string
     */
    private function guessLocale(Request $request, string $localeCookieName, array $supportedLocales): string
    {
        return
            $request->query->get('locale') ??
            $request->cookies->get($localeCookieName) ??
            $request->getPreferredLanguage($supportedLocales);
    }
}