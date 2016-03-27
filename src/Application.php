<?php

namespace Domynation;

use DI\ContainerBuilder;
use Doctrine\Common\Cache\ApcuCache;
use Domynation\Config\ConfigInterface;
use Domynation\Config\InMemoryConfigStore;
use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\Router;
use Domynation\Session\PHPSession;
use Domynation\Session\SessionInterface;
use Domynation\View\ViewFactoryInterface;

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

        // Boot the configuration
        $config = $this->bootConfiguration();

        // Default timezone
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
     */
    private function bootContainer(ConfigInterface $config, SessionInterface $session)
    {
        $builder = new ContainerBuilder();
        $builder->useAnnotations(false);
        $builder->setDefinitionCache(new ApcuCache());

        // Load all global services
        $builder->addDefinitions(array_merge(require_once __DIR__ . '/Config/container.php', [
            ConfigInterface::class  => $config,
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
                $container->get(Router::class),
                $container->get(ViewFactoryInterface::class),
                $container->get(EventDispatcherInterface::class)
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
     * @return void
     */
    private function bootErrorReporting()
    {
        error_reporting(-1);

        // @todo: Set the error handling
    }
}