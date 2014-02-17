<?php

namespace Foogile\WpCli\Migrate;

interface MigrationInterface
{
    const   STATUS_UP = true,
            STATUS_DOWN = false;
    
    public function up();
    public function down();
    public function getVersion();
    public function isUp();
    public function isDown();
    public function setStatus($status);
}
