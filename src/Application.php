<?php

namespace Domynation;

use DI\ContainerBuilder;
use Doctrine\Common\Cache\ApcuCache;
use Domynation\Config\ConfigInterface;
use Domynation\Config\InMemoryConfigStore;
use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\Router;
use Domynation\Http\RouterInterface;
use Domynation\Session\PHPSession;
use Domynation\Session\SessionInterface;
use Domynation\View\ViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class Application
{

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var \Interop\Container\ContainerInterface
     */
    private $container;

    /**
     * @var Request
     */
    private $request;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Boots the application.
     *
     * @return void
     */
    public function boot()
    {
        // @todo: Remove this once we get rid of global constants and replace them with dotenv
        define('PATH_BASE', $this->basePath);
        define('PATH_HTML', $this->basePath . '/views/');

        // Boot the error reporting
        $this->bootErrorReporting();

        // Boot the request
        $this->request = $this->bootRequest();

        // Boot the configuration
        $config = $this->bootConfiguration();

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
     * @return \DI\Container
     * @throws \DI\NotFoundException
     */
    private function bootContainer(ConfigInterface $config, SessionInterface $session)
    {
        $builder = new ContainerBuilder();
        $builder->useAnnotations(false);

        // Cache the definitions in production
        if (IS_PRODUCTION) {
            $builder->setDefinitionCache(new ApcuCache());
        }

        // Load all global services
        $builder->addDefinitions(array_merge(require_once __DIR__ . '/Files/container.php', [
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
            $provider   = $reflection->newInstanceArgs();

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
                $container->get(EventDispatcherInterface::class),
                $container->get(Router::class)
            );

            // Use reflection to get a closure copy of the `boot` method
            $reflection = new \ReflectionClass(get_class($provider));
            $closure    = $reflection->getMethod('boot')->getClosure($provider);

            // Let the container inject the dependency needed by the closure
            $container->call($closure);
        }

        return $container;
    }

    /**
     * Boots the session.
     *
     * @return \Domynation\Session\PHPSession
     */
    private function bootSession()
    {
        $session = new PHPSession;

        $session->start();

        return $session;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @return \Interop\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return void
     */
    private function bootConfiguration()
    {
        require_once $this->basePath . '/env.php';

        return new InMemoryConfigStore(require_once $this->basePath . '/config/application.php');
    }

    /**
     * Executes the request and returns the response.
     *
     * @return mixed
     */
    public function run()
    {

//        $whoops = new \Whoops\Run();
//        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
//        $whoops->register();
        $router = $this->container->get(RouterInterface::class);
//        $exceptionHandler = $this->container->get(ExceptionHandlerInterface::class);

        // Resolve the controller and arguments for the requested route
        try {
            $response = $router->handle($this->request);
        } catch (\Exception $e) {
            echo $e;

            return;
        }

        // Send the response
        $response->send();
    }

    private function handleException(\Exception $e)
    {
        if ($e instanceof \Symfony\Component\Routing\Exception\RouteNotFoundException) {
            return new \Symfony\Component\HttpFoundation\Response('', HTTP_NOT_FOUND);
        }

        if ($e instanceof \Domynation\Exceptions\AuthenticationException) {
            return new \Symfony\Component\HttpFoundation\Response('', HTTP_AUTHENTICATION_REQUIRED);
        }

        if ($e instanceof \Domynation\Exceptions\AuthorizationException) {
            return new \Symfony\Component\HttpFoundation\Response('', HTTP_FORBIDDEN);
        }

        if ($e instanceof \Domynation\Exceptions\ValidationException) {
            return new \Symfony\Component\HttpFoundation\JsonResponse(['errors' => $e->errors()], HTTP_BAD_REQUEST);
        }

        if ($e instanceof \Domynation\Exceptions\EntityNotFoundException) {
            return new \Symfony\Component\HttpFoundation\JsonResponse(['errors' => [$e->getMessage()]], HTTP_BAD_REQUEST);
        }

        return new \Symfony\Component\HttpFoundation\JsonResponse(['errors' => [$e->getMessage()]], HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Boots error reporting.
     *
     * @return void
     */
    private function bootErrorReporting()
    {
        error_reporting(-1);

        // @todo: Set the error handling
    }

    /**
     * Boots the request.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function bootRequest()
    {
        return Request::createFromGlobals();
    }
}