
<?php
	$configs = include('config_example.php');

    class Connection
    {
        private static $instance = null;
        private $con = null;


        protected function __construct() 
        { 
            $this->dbConnection();
        }
        
        
        /** Creates a singular instance */
        public static function getInstance()
        {
            if (self::$instance == null)
                self::$instance = new Connection();
        
            return self::$instance;
        }


        /** Create connection to the database */
        private function dbConnection() 
        {
        	global $configs;
        	
            $host = $configs->host;
            $username = $configs->username;
            $password = $configs->password;
            $database = $configs->database;

            $connect = "mysql:host=" . $host . "; dbname=" . $database . "; charset=utf8";

            try {
                $this->con = new PDO($connect, $username, $password);
                $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } 
            catch (PDOException $e) {
                echo "<script> alert('ERROR \nConnection failed: " . $e->getMessage() . "') </script>";
            }
        }
        

        public function getConnection(): PDO { return $this->con; }
    }
?>