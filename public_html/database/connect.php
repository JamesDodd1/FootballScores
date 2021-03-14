
<?php
	//$configs = include('config_example.php');

    class Connection
    {
        /** @var PDO */
        private $connection = null;


        public function __construct() 
        { 
            $this->connection = null;
        }


        public function __destruct()
        {
            $this->disconnect();
        }


        /** Connect to a database */
        public function connect(string $host, string $username, string $password, string $database) 
        {
            $isConnectedToDatabase = !is_null($this->connection);
            if ($isConnectedToDatabase) { return; }


            $connect = "mysql:host=$host; dbname=$database; charset=utf8";

            try {
                $pdo = new PDO($connect, $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $this->connection = $pdo;
            } 
            catch (PDOException $e) {
                echo "<script> alert('ERROR \nConnection failed: " . $e->getMessage() . "') </script>";
            }
        }


        /** Disconnect from the database */
        public function disconnect()
        {
            $this->connection = null;
        }
        

        public function getConnection(): ?PDO { return $this->connection; }
    }
?>