<?php

namespace Foogile\WpCli\Migrate;

use Foogile\WpCli\Migrate\MigrationRepository;

class MigrationProxy implements MigrationInterface
{
    /**
     * @var MigrationInterface
     */
    private $migration;
    
    /**
     * @var MigrationRepository
     */
    private $repository;
    
    public function __construct(MigrationInterface $migration, MigrationRepository $repository)
    {
        $this->migration = $migration;
        $this->repository = $repository;
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
        return $this->migration->getVersion();
    }

    public function isDown()
    {
        return $this->migration->isDown();
    }

    public function isUp()
    {
        return $this->migration->isUp();
    }

    public function setStatus($status)
    {
        $this->migration->setStatus($status);
        $this->repository->save($this);
    }
}
