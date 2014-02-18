<?php

namespace Foogile\WpCli\Migrate;

interface MigrationInterface
{
    public function up();
    public function down();
}
