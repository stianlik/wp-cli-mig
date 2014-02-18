<?php

namespace Foogile\WpCli\Migrate;

use Foogile\WpCli\Migrate\MigrationRepository;

class MigrationProxy implements MigrationInterface
{
    const   STATUS_UP = true,
            STATUS_DOWN = false;
    
    private $version;
    private $status;
    
    /**
     * @var MigrationInterface
     */
    private $migration;
    
    /**
     * @var MigrationRepository
     */
    private $repository;
    
    public function __construct(MigrationInterface $migration, MigrationRepository $repository, $version, $status = self::STATUS_DOWN)
    {
        $this->migration = $migration;
        $this->repository = $repository;
        $this->version = (int) $version;
        $this->setStatus($status);
    }

    public function up()
    {
        $this->migration->up();
        $this->setStatus(self::STATUS_UP);
    }
    
    public function down()
    {
        $this->migration->down();
        $this->setStatus(self::STATUS_DOWN);
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function isDown()
    {
        return !$this->status;
    }

    public function isUp()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = (bool) $status;
        $this->repository->save($this);
    }
}
