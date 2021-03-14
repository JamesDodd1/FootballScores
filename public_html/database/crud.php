
<?php
    //require_once 'connect.php';

    class Crud
    {
        private $databaseConnection;

        public function __construct(PDO $databaseConnection)
        {
            $this->databaseConnection = $databaseConnection; 
        } 


        public function select(string $sql, array $fields = [])
        {
            if (is_null($this->databaseConnection)) { return null; }


            try {
                $query = $this->databaseConnection->prepare($sql);
                $query->execute($fields);

                $numOfRows = $query->rowCount();

                if ($numOfRows == 0)
                    return null;
                else if ($numOfRows == 1)
                    return $query->fetch(PDO::FETCH_OBJ);
                else 
                    return $query->fetchAll(PDO::FETCH_OBJ);
            } 
            catch (Exception $e) {
                echo "<script> alert('Error: ".$e->getMessage()."') </script>";
            }

            return null;
        }


        /** Runs SQL */
        public function runSQL(string $sql, array $fields = [])
        {
            if (is_null($this->databaseConnection)) { return false; }


            try {
                $query = $this->databaseConnection->prepare($sql);
                $query->execute($fields);

                return $query->rowCount() ? true : false;

            } catch (Exception $e) {
                echo "<script> alert('Error: " . $e->getMessage() . "') </script>";
            }

            return false;
        }
    }
?>
