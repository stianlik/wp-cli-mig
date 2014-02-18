<?php

namespace Foogile\WpCli\Migrate;

use Foogile\WpCli\Migrate\Storage;

class MigrationRepository
{
    const OPTION_NAME = 'Foogile\\WPCli\\Migrate\Migrations';
    
    private $options;
    private $path;
    private $namespace;
    private $pattern = '#([0-9]+)_([A-Z][A-Za-z0-9]*)\.php#';
    
    /**
     * @var Storage
     */
    private $storage;
    
    public function __construct(Storage $storage, $path = '.', $namespace = '\WpCliMigration')
    {
        $this->path = $path;
        $this->namespace = $namespace;
        $this->storage = $storage;
        $this->options = $this->storage->get(self::OPTION_NAME, array());
    }
    
    /**
     * @param int $version
     * @return mixed Migration or false if it does not exist
     */
    public function getMigration($version)
    {
        $iter = new \DirectoryIterator($this->path);
        foreach ($iter as $dir) {
            $filename = $dir->getFilename();
            if ($this->isMigrationFile($filename) && $this->getMigrationVersion($filename) == $version) {
                return $this->createMigration($filename);
            }
        }
        return false;
    }
    
    public function getMigrations()
    {
        $migrations = array();
        $iter = new \DirectoryIterator($this->path);
        foreach ($iter as $dir) {
            if ($this->isMigrationFile($dir->getFilename())) {
                $migrations[] = $this->createMigration($dir->getFilename());
            }
        }
        return $migrations;
    }
    
    public function isMigrationFile($filename)
    {
        return preg_match($this->pattern, $filename) === 1;
    }
    
    public function getMigrationClassName($filename)
    {
        $matches = array();
        preg_match($this->pattern, $filename, $matches);
        return $this->namespace . '\\' . $matches[2];
    }
    
    public function getMigrationVersion($filename)
    {
        $matches = array();
        preg_match($this->pattern, $filename, $matches);
        return $matches[1];
    }
    
    public function createMigration($filename)
    {
        require_once "{$this->path}/$filename";
        $className = $this->getMigrationClassName($filename);
        $version = $this->getMigrationVersion($filename);
        $isUp = !empty($this->options[$version]) ? true : false;
        return new MigrationProxy(new $className(), $this, $version, $isUp);
    }
    
    public function save(MigrationProxy $migration)
    {
        $this->options[$migration->getVersion()] = $migration->isUp();
    }
    
    public function persist()
    {
        $this->storage->update(self::OPTION_NAME, $this->options);
    }
}
