wp-cli-mig
==========

General migration command for WP-CLI

## Example migration

1. Create migration script

    ```PHP
    // filename: migrations/1_FirstMigration.php
    namespace WpCliMigrate;

    class FirstMigration extends \Foogile\WpCli\Migrate\AbstractMigration
    {

        public function up()
        {
            // Do some work using WordPress API
        }

        public function down()
        {
            // Undo some work using WordPress API
        }

    }
    ```

2. Execute script using WP-CLI
    
    ```Shell
        # Migrate to version 1
        wp --require=command.php mig to 1

        # Migrate to version 2
        wp --require=command.php mig to 2
        
        # Revert all migrations
        wp --require=command.php mig to 0

        # Status
        wp --require=command.php mig status
    ```

For migrations that should stop execution, throw exceptions from up/down-methods. I.e.
a non-reversable migration will typically refuse a `down()`-operation: `throw new \Exception("Cannot rollback migration")`.
