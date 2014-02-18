<?php

namespace Foogile\Test\WpCli\Migrate;

use Foogile\WpCli\Migrate\MigrationRepository;

class MigrationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @var MigrationRepository
     */
    private $repository;
    
    private $storageMock;
    
    public function setUp()
    {
        parent::setUp();
        $this->storageMock = $this->getMock('Foogile\WpCli\Migrate\Storage', array('get', 'update'));
        $this->repository = new MigrationRepository($this->storageMock, __DIR__ . '/fixtures', '\TestMigration');
    }
    
    /**
     * @dataProvider validMigrationFileProvider
     */
    public function test_isMigrationFile_returns_true_for_valid_files($filename)
    {
        $this->assertTrue($this->repository->isMigrationFile($filename));
    }
    
    /**
     * @dataProvider invalidMigrationFileProvider
     */
    public function test_isMigrationFile_returns_false_for_invalid_files($filename)
    {
        $this->assertFalse($this->repository->isMigrationFile($filename));
    }
    
    /**
     * @dataProvider classNameMappingProvider
     */
    public function test_getMigrationClassName($expectedClassName, $filename)
    {
        $this->assertEquals($expectedClassName, $this->repository->getMigrationClassName($filename));
    }
    
    public function test_createMigration()
    {
        $migration = $this->repository->createMigration('1_FirstMigration.php');
        $this->assertEquals(1, $migration->getVersion());
        $this->assertInternalType('int', $migration->getVersion());
        $this->assertFalse($migration->isUp());
    }
    
    public function test_getMigrations_returns_all_migrations()
    {
        $migrations = $this->repository->getMigrations();
        $this->assertCount(2, $migrations);
    }
        
    public function test_getMigration_returns_false_if_migration_does_not_exist()
    {
        $this->assertFalse($this->repository->getMigration(3));
    }
    
    /**
     * @dataProvider migrationVersionProvider
     */
    public function test_getMigration_returns_one_migration_of_given_version($version)
    {
        $migration = $this->repository->getMigration($version);
        $this->assertEquals($version, $migration->getVersion());
    }
    
    public function test_getMigrations_returns_ordered_list_of_results()
    {
        $this->repository->setPath(__DIR__ . '/fixtures/sort');
        $migrations = $this->repository->getMigrations();
        $this->assertEquals(5, $migrations[0]->getVersion());
        $this->assertEquals(10, $migrations[1]->getVersion());
        $this->assertEquals(11, $migrations[2]->getVersion());
    }
    
    public function migrationVersionProvider()
    {
        return array(
            array(1),
            array(2)
        );
    }

    public function classNameMappingProvider()
    {
        return array(
            array('\TestMigration\Class', '0_Class.php'),
            array('\TestMigration\SomeOtherClass', '123_SomeOtherClass.php')
        );
    }
    
    public function validMigrationFileProvider()
    {
        return array(
            array('0_Class.php'),
            array('12_SomeOtherClass.php')
        );
    }
    
    public function invalidMigrationFileProvider()
    {
        return array(
            array('Class.php'),
            array('class'),
            array('0_class.php'),
            array('0_Class'),
            array('123_SomeClass'),
            array('123_someClass.php'),
            array('0_Some_Class.php')
        );
    }
}
