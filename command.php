<?php

if (defined('WP_CLI') && \WP_CLI) {
    \WP_CLI::add_command('mig', '\Foogile\WpCli\Migrate\MigrateCommand');
}