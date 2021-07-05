<?php
namespace Tests\Unit\Database;

require_once 'vendor/autoload.php';

use Database\Crud;
use Exception;
use PDO;
use PHPUnit\Framework\TestCase;
use ValueError;

class CrudTest extends TestCase
{
    /** @var \PDO */
    protected static $database;

    public static function setUpBeforeClass(): void
    {
        $config = include 'config/database.php';
        echo __DIR__;
        $db = $config->connections->test;
        
        self::$database = new \PDO(
            "mysql:host=$db->host; dbname=$db->database; charset=utf8",
            $db->username,
            $db->password
        );
    }

    public static function tearDownAfterClass(): void
    {
        self::$database == null;
    }

    public function setUp(): void
    {
        self::$database->beginTransaction();
    }


    public function tearDown(): void
    {
        self::$database->rollBack();
    }


    /** @test */
    public function insert_AddToExistingTable_ReturnsTrue()
    {
        $crud = new Crud(self::$database);

        $result = $crud->insert("INSERT INTO `User` (`UserID`, `Name`, `Answers`) VALUES (1, '', 0);");

        $this->assertTrue($result);
    }


    /** @test */
    public function insert_InvalidQuery_ThrowsValueError()
    {
        $this->expectException(ValueError::class);

        $crud = new Crud(self::$database);

        $crud->insert("");
    }


    /** @test */
    public function select_RetrievesExistingData_ReturnsArray()
    {
        $crud = new Crud(self::$database);
        self::$database->exec(
            "INSERT INTO `User` (`UserID`, `Name`, `Answers`) VALUES (1, '', 0);"
        );
        $expected = [ (object) [ 'UserID' => '1', 'Name' => '', 'Answers' => '0' ] ];

        $actual = $crud->select("SELECT * FROM `User`;");
        
        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function select_RetrievesNoData_ReturnsEmptyArray()
    {
        $crud = new Crud(self::$database);
        $expected = [];

        $actual = $crud->select("SELECT * FROM `User`;");
        
        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function select_InvalidQuery_ThrowsValueError()
    {
        $this->expectException(ValueError::class);

        $crud = new Crud(self::$database);
        
        $crud->select("");
    }


    /** @test */
    public function update_ChangeExistingData_ReturnsTrue()
    {
        $crud = new Crud(self::$database);
        self::$database->exec(
            "INSERT INTO `User` (`UserID`, `Name`, `Answers`) VALUES (1, '', 0);"
        );
        
        $result = $crud->update("UPDATE `User` SET `Name` = '' WHERE `UserID` = 1;");

        $this->assertTrue($result);
    }


    /** @test */
    public function update_InvalidQuery_ThrowsValueError()
    {
        $this->expectException(ValueError::class);

        $crud = new Crud(self::$database);
        
        $crud->update("");
    }


    /** @test */
    public function delete_RemoveValue_ReturnsTrue()
    {
        $crud = new Crud(self::$database);
        self::$database->exec(
            "INSERT INTO `User` (`UserID`, `Name`, `Answers`) VALUES (1, '', 0);"
        );

        $result = $crud->update("DELETE FROM `User` WHERE `UserID` = 1;");

        $this->assertTrue($result);
    }


    /** @test */
    public function delete_InvalidQuery_ThrowsValueError()
    {
        $this->expectException(ValueError::class);

        $crud = new Crud(self::$database);

        $crud->update("");
    }
}

?>
