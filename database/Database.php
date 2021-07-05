<?php
namespace Database;

use Exception;
use PDO;

class Database
{
    /** @var PDO */
    private $connection = null;


    public function __destruct()
    {
        $this->disconnect();
    }


    /**
     * Connect to a database
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     * 
     */
    public function connect(string $host, string $username, string $password, string $database) 
    {
        $isConnectedToDatabase = !is_null($this->connection);
        if ($isConnectedToDatabase) { throw new Exception("Database connection is already set"); }

        try {
            $pdo = new PDO("mysql:host=$host; dbname=$database; charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->connection = $pdo;

            return true;
        }
        catch (Exception $e) {
            //Log::write($e->getMessage());
            throw $e;
        }
    }


    /** Disconnect from the database */
    public function disconnect()
    {
        $this->connection = null;
    }


    /**
     * Retrieves the database connection
     * @return null|PDO
     */
    public function getConnection(): ?PDO { return $this->connection; }
}

?>
