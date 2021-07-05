<?php
namespace Tests\Unit\Database;

require_once 'vendor/autoload.php';

use Database\Database;
use Exception;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    /** @test */
    public function connect_FailToConnectToDatabase_ThrowsException()
    {
        $this->expectException(Exception::class);

        $database = new Database();

        $database->connect('', '', '', '');
    }


    /** @test */
    public function connect_FailsConnectToDatabase_ReturnsTrue()
    {
        $config = include "config/database.php";
        $db = $config->connections->localhost;
        $database = new Database();

        $result = $database->connect($db->host, $db->username, $db->password, $db->database);

        $this->assertTrue($result);
    }


    /** @test */
    public function connect_AttemptToConnectWhileAlreadyConnected_ThrowsException()
    {
        $this->expectException(Exception::class);

        $config = include "config/database.php";
        $db = $config->connections->localhost;
        $database = new Database();
        $database->connect($db->host, $db->username, $db->password, $db->database);

        $database->connect($db->host, $db->username, $db->password, $db->database);
    }


    /** @test */
    public function disconnect_CloseConnectionToDatabase_ReturnsNull()
    {
        $config = include "config/database.php";
        $db = $config->connections->localhost;
        $database = new Database();
        $database->connect($db->host, $db->username, $db->password, $db->database);

        $database->disconnect();
        
        $this->assertNull($database->getConnection());
    }
}

?>
