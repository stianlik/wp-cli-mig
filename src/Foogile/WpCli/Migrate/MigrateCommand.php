<?php

namespace Foogile\WpCli\Migrate;

use WP_CLI;
use Exception;

/**
 * Database migrations
 */
class MigrateCommand
{
    /**
     * @var MigrationRepository
     */
    private $repository;
    
    
    /**
     * Migrate to version (defaults to latest)
     * @synopsis [--mig-path=<mig-path>] [--mig-namespace=<mig-namespace>] [<version>]
     */
    public function to($args, $assocArgs)
    {
        $version = isset($args[0]) ? (int) $args[0] : PHP_INT_MAX;
        $loader = $this->getRepository($assocArgs);
        $migrations = $loader->getMigrations();
        try {
            // Up
            foreach ($migrations as $migration) {
                if ($migration->getVersion() > $version) {
                    break;
                }
                if ($migration->isDown()) {
                    $this->migrateUp($migration);
                }
            }
            
            // Down migrations in reverse
            foreach (array_reverse($migrations) as $migration) {
                if ($migration->getVersion() <= $version) {
                    break;
                }
                if ($migration->isUp()) {
                    $this->migrateDown($migration);
                }
            }
        } catch (Exception $ex) {
            WP_CLI::error("Migration failed with message '{$ex->getMessage()}'");
        }
        
        WP_CLI::success('Migrated to ' . (($version === PHP_INT_MAX) ? 'latest version' : "version '$version'") );
    }
        
    /**
     * Status for migration(s)
     * @synopsis [--mig-path=<mig-path>] [--mig-namespace=<mig-namespace>] [<version>]
     */
    public function status($args, $assocArgs)
    {
        if (isset($args[0])) {
            $migration = $this->getMigration($args, $assocArgs);
            $this->statusLine($migration);
        } else {
            $migrations = $this->getRepository($assocArgs)->getMigrations();
            foreach ($migrations as $migration) {
                $this->statusLine($migration);
            }
        }
    }
    
    /**
     * Up migration
     * @synopsis [--mig-path=<mig-path>] [--mig-namespace=<mig-namespace>] <version>
     */
    public function up($args, $assocArgs)
    {
        $version = $args[0];
        $migration = $this->getMigration($args, $assocArgs);
        if ($migration->isUp()) {
            WP_CLI::warning("Migration already up, no action performed");
            return;
        }
        try {
            $this->migrateUp($migration);
        } catch (Exception $ex) {
            WP_CLI::error("Up migration '$version' failed with message '{$ex->getMessage()}'");
        }
        WP_CLI::success("Up migration '$version' completed");
    }
    
    /**
     * Down migration
     * @synopsis [--mig-path=<mig-path>] [--mig-namespace=<mig-namespace>] <version>
     */
    public function down($args, $assocArgs)
    {
        $version = $args[0];
        $migration = $this->getMigration($args, $assocArgs);
        if ($migration->isDown()) {
            WP_CLI::warning("Migration already down, no action performed");
            return;
        }
        try {
            $this->migrateDown($migration);
        } catch (Exception $ex) {
            WP_CLI::error("Down migration '$version' failed with message '{$ex->getMessage()}'");
        }
        WP_CLI::success("Down migration '$version' completed");
    }
    
    /**
     * @return MigrationProxy
     */
    private function getMigration($args, $assocArgs) {
        $loader = $this->getRepository($assocArgs);
        $migration = $loader->getMigration($args[0]);
        if (!$migration) {
            WP_CLI::error("Migration version '{$args[0]}' does not exit");
        }
        return $migration;
    }
    
    /**
     * @param array $assocArgs
     * @return MigrationRepository
     */
    private function getRepository($assocArgs = array())
    {
        if ($this->repository === null) {
            $path = isset($assocArgs['mig-path']) ? $assocArgs['mig-path'] : '.';
            $namespace = isset($assocArgs['mig-namespace']) ? $assocArgs['mig-namespace'] : 'WpCliMigrate';
            $this->repository = new MigrationRepository(new Storage(), $path, $namespace);
        }
        return $this->repository;
    }
    
    private function migrateUp($migration)
    {
        $migration->up();
        $this->getRepository()->persist();
        WP_CLI::line("Up migration '{$migration->getVersion()}'");
    }
    
    private function migrateDown($migration)
    {
        $migration->down();
        $this->getRepository()->persist();
        WP_CLI::line("Down migration '{$migration->getVersion()}'");
    }
    
    private function statusLine($migration)
    {
        $status = $migration->isUp() ? WP_CLI::colorize('%gUP%N') : WP_CLI::colorize('%rDOWN%N');
        WP_CLI::line("Migration {$migration->getVersion()}: $status");
    }
}
