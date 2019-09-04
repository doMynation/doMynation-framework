<?php

namespace Domynation;

use DI\ContainerBuilder;
use Domynation\Config\ConfigInterface;
use Domynation\Config\InMemoryConfigStore;
use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\RouterInterface;
use Domynation\Session\PHPSession;
use Domynation\Session\SessionInterface;
use Domynation\View\ViewFactoryInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Whoops\Handler\Handler;
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
    private $basePath;

    /**
     * The environment the kernel is operating in.
     * Possible values: web, test
     *
     * @var string
     */
    private $environment;

    /**
     * The dependency injection container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * The current HTTP request.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * Application constructor.
     *
     * @param string $basePath
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
     */
    public function boot(): void
    {
        // Boot the configuration
        $config = $this->bootConfiguration();

        // Boot the error reporting
        $this->bootErrorReporting();

        // Boot the request
        $this->request = $this->bootRequest();

        // Set the default timezone
        date_default_timezone_set(DATE_TIMEZONE);

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
     */
    private function bootContainer(ConfigInterface $config, SessionInterface $session): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->useAnnotations(false);

        // Cache the definitions in production
        if (IS_PRODUCTION) {
            $builder->enableCompilation($this->basePath . '/cache');
        }

        // Load all global services
        $builder->addDefinitions(array_merge(require __DIR__ . '/Files/container.php', [
            ConfigInterface::class  => $config,
            Request::class          => $this->request,
            SessionInterface::class => $session
        ]));

        // First pass: Instanciate each providers and load their container definitions
        $providers = array_reduce($config->get('providers'), function ($accumulator, $name) use ($builder) {
            if (!class_exists($name)) {
                return $accumulator;
            }

            // Instanciate the provider
            $reflection = new \ReflectionClass($name);
            $provider = $reflection->newInstanceArgs();

            // Load the provider's container definitions
            $builder->addDefinitions($provider->registerContainerDefinitions());

            $accumulator[] = $provider;

            return $accumulator;
        });

        // Build the container
        $container = $builder->build();

        // Second pass: Boot each service
        foreach ($providers as $provider) {
            // Start the provider
            $provider->start(
                $container->get(RouterInterface::class),
                $container->get(ViewFactoryInterface::class),
                $container->get(EventDispatcherInterface::class)
            );

            // Use reflection to get a closure copy of the `boot` method
            $reflection = new \ReflectionClass(get_class($provider));
            $closure = $reflection->getMethod('boot')->getClosure($provider);

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
        $session = new PHPSession;

        if ($this->environment === 'web') {
            $session->start();
        }

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
        // Load application configurations
        $applicationConfig = require $this->basePath . '/config/application.php';
        $applicationConfig['basePath'] = $this->basePath;
        $applicationConfig['environment'] = $this->environment;

        // Load environment configurations
        // @todo: Move these to application configurations
        require_once $this->basePath . '/config/env.php';

        return new InMemoryConfigStore($applicationConfig);
    }

    /**
     * Executes the request and returns the response.
     *
     * @return mixed
     */
    public function run()
    {
        $router = $this->container->get(RouterInterface::class);

        // Resolve the controller and arguments for the requested route
        $response = $router->handle($this->request);

        // Send the response
        $response->send();
    }

    /**
     * @param \Exception|\Throwable $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @todo: Move this to userland and allow customization.
     *
     */
    private function handleException(\Throwable $e)
    {
        if ($e instanceof \Symfony\Component\Routing\Exception\RouteNotFoundException) {
            return new Response('Page not found', Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof \Domynation\Exceptions\AuthenticationException) {
            if ($this->request->isXmlHttpRequest()) {
                return new Response('Forbidden', Response::HTTP_UNAUTHORIZED);
            }

            return new RedirectResponse('/login');
        }

        if ($e instanceof \Domynation\Exceptions\AuthorizationException) {
            if ($this->request->isXmlHttpRequest()) {
                return new Response('Unauthorized', Response::HTTP_FORBIDDEN);
            }

            return new RedirectResponse('/403');
        }

        if ($e instanceof \Domynation\Exceptions\ValidationException) {
            return new JsonResponse(['errors' => $e->getErrors()], Response::HTTP_BAD_REQUEST);
        }

        if ($e instanceof \Domynation\Exceptions\EntityNotFoundException) {
            return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }

        if ($e instanceof \Sushi\Common\Exceptions\DomainException) {
            return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return false;
    }

    /**
     * Boots error reporting.
     *
     * @return void
     */
    private function bootErrorReporting(): void
    {
        // Report all errors
        error_reporting(-1);

        $debugHandler = DEBUG ? new PrettyPageHandler : function ($exception, $inspector, $run) {
            echo "Whoops, something went wrong...";
        };

        $whoops = new Run();
        $whoops->pushHandler($debugHandler);
        $whoops->pushHandler(function ($exception, $inspector, $run) {
            if ($response = $this->handleException($exception)) {
                $response->send();

                return Handler::QUIT;
            }
        });

        $whoops->register();
    }

    /**
     * Boots the request.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function bootRequest(): Request
    {
        return Request::createFromGlobals();
    }
}