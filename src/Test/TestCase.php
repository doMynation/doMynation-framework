<?php

namespace Domynation\Test;

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Command\DumpSchemaCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\RollupCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Doctrine\ORM\EntityManager;
use \Doctrine\DBAL\Connection;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class TestCase
 *
 * @package Domynation\Test
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * True when the database was migrated and seeded.
     */
    protected static bool $isMigrated = false;

    /**
     * The framework kernel.
     *
     * @var \Domynation\Application
     */
    protected $kernel;

    /**
     * The dependency injection container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * The ORM.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * The DBAL.
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    public function setUp(): void
    {
        $this->initialize();

        // Only migrate and seed the database if it hasn't already been migrated
        if (!static::$isMigrated) {
            $this->migrateDatabase();

            static::$isMigrated = true;
        }
    }

    /**
     * Finds an entry in the DI container and returns it.
     *
     * @param string $className
     *
     * @return mixed
     */
    public function inject(string $className)
    {
        return $this->container->get($className);
    }

    /**
     * Initializes the framework kernel and a few useful dependencies.
     */
    private function initialize()
    {
        // Boot the kernel
        $this->kernel = new \Domynation\Application(PATH_BASE, 'test');
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();
        $this->em = $this->container->get(EntityManager::class);
        $this->db = $this->em->getConnection();
    }

    /**
     * Wipes the database and recreates the schema by running
     * all database migrations.
     *
     * @throws \Exception
     */
    private function migrateDatabase(): void
    {
        $configuration = new Configuration($this->db);
        $configuration->setName('Integration Test Migrations');
        $configuration->setMigrationsNamespace('Sushi\Migrations');
        $configuration->setMigrationsTableName('doctrine_migration_versions');
        $configuration->setMigrationsColumnName('version');
        $configuration->setMigrationsColumnLength(255);
        $configuration->setMigrationsExecutedAtColumnName('executed_at');
        $configuration->setMigrationsDirectory($this->kernel->getBasePath() . '/data/Migrations');
        $configuration->setAllOrNothing(true);
        $configuration->setCheckDatabasePlatform(false);

        $helperSet = new HelperSet();
        $helperSet->set(new QuestionHelper(), 'question');
        $helperSet->set(new ConnectionHelper($this->db), 'db');
        $helperSet->set(new ConfigurationHelper($this->db, $configuration));

        $cli = new Application('Migrations for integration tests');
        $cli->setCatchExceptions(true);
        $cli->setAutoExit(false);
        $cli->setHelperSet($helperSet);
        $cli->addCommands([
            new DumpSchemaCommand(),
            new ExecuteCommand(),
            new GenerateCommand(),
            new LatestCommand(),
            new MigrateCommand(),
            new RollupCommand(),
            new StatusCommand(),
            new VersionCommand()
        ]);

        // Revert to the very first migration (wipes all tables)
        $output = new BufferedOutput();
        $exitCode = $cli->run(new ArrayInput([
            'command'          => 'migrations:execute',
            'version'          => 'Initial',
            '--down'           => '',
            '--no-interaction' => '',
        ]), $output);

        if ($exitCode !== 0) {
            echo "An error occurred while running migration: {$exitCode}";
            echo $output->fetch();
            exit;
        }

        // Re-apply all migrations
        $exitCode = $cli->run(new ArrayInput([
            'command'          => 'migrations:migrate',
            '--no-interaction' => '',
        ]), $output);

        if ($exitCode !== 0) {
            echo "An error occurred while running migration: {$exitCode}";
            echo $output->fetch();
            exit;
        }
    }
}