<?php

namespace Foogile\WpCli\Migrate;

abstract class AbstractMigration implements MigrationInterface
{
    private $version;
    private $status;
    
    public function __construct($version, $isUp = false)
    {
        $this->version = (int) $version;
        $this->status = $isUp;
    }
    
    public function getVersion()
    {
        return $this->version;
    }
    
    public function isUp()
    {
        return $this->status;
    }

    public function isDown()
    {
        return !$this->isUp();
    }
    
    public function setStatus($status)
    {
        $this->status = $status;
    }
}
