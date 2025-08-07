<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use Core\Database;
use Core\Session;

class BaseFeatureTestCase extends TestCase
{
    protected $pdo;
    protected $container;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {

        // Bootstrap application to get the controller
        $this->container = require __DIR__ . '/../../bootstrap.php';

        // Start the session for testing
        Session::start();

        $this->pdo = Database::getInstance()->getPdo();

        // Run all migrations to set up the schema
        $this->runMigrations();
    }

    /**
     * This method gets called after each test
     */
    protected function tearDown(): void
    {
        // Destroy the session
        Session::destroy();

        // This is not strictly necessary with an in-memory DB,
        // but good practice for file-based test DBs.
        $this->pdo = null;
        $this->container = null;
        parent::tearDown();
    }

    private function runMigrations()
    {
        $migrationFiles = glob('database/migrations/*.php');
        foreach ($migrationFiles as $file) {
            require_once $file;
            $className = substr(basename($file, '.php'), 18);
            if (class_exists($className)) {
                $migration = new $className();
                $migration->up();
            }
        }
    }
}
