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
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * True when the database was migrated and seeded.
     *
     * @var bool
     */
    protected static $isMigrated = false;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    /**
     * @var \Domynation\Application
     */
    protected $kernel;

    public function setUp(): void
    {
        $this->initializeKernel();
        $this->initializeDatabase();

        // Only migrate and seed the database if it hasn't alread been migrated
        if (!static::$isMigrated) {
            $this->createSchema();

            static::$isMigrated = true;
        }

        $this->db->beginTransaction();;
    }

    public function tearDown(): void
    {
        $this->db->rollBack();
    }

    private function initializeKernel()
    {
        $this->kernel = new \Domynation\Application(PATH_BASE, 'test');
        $this->kernel->boot();
    }

    private function initializeDatabase()
    {
        $config = require $this->kernel->getBasePath() . '/config/application.php';

        $this->em = $this->kernel->getContainer()->get(EntityManager::class);
        $this->db = $this->em->getConnection();
    }

    private function createSchema(): void
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
        $cli->run(new ArrayInput([
            'command'          => 'migrations:execute',
            'version'          => 'Initial',
            '--down'           => '',
            '--no-interaction' => '',
        ]), $output);

        // Re-apply all migrations
        $cli->run(new ArrayInput([
            'command'          => 'migrations:migrate',
            '--no-interaction' => '',
        ]), $output);
    }

    protected function assertRecordExists(string $tableName, $id): void
    {
        $record = $this->db->fetchAssoc("SELECT * FROM $tableName WHERE id=?", [$id]);

        $this->assertNotEmpty($record, "Failed asserting that record of id $id exists in table $tableName");
    }
}