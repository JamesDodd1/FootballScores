
<?php
    require_once 'connect.php';

    class Crud
    {
        private $con;

        public function __construct()
        {
            $connect = Connection::getInstance();
            $this->con = $connect->getConnection(); 
        } 

        protected function select(string $sql, array $fields = [])
        {
            try {
                $query = $this->con->prepare($sql);
                $query->execute($fields);

                $count = $query->rowCount();

                if ($count == 0)
                    return null;
                else if ($count == 1)
                    return $query->fetch(PDO::FETCH_OBJ);
                else 
                    return $query->fetchAll(PDO::FETCH_OBJ);
            } 
            catch (Exception $e) 
            {
                echo "<script> alert('Error: ".$e->getMessage()."') </script>";

                return null;
            }
        }

        /** Runs SQL */
        protected function runSQL(string $sql, array $fields = [])
        {
            try {
                $query = $this->con->prepare($sql);
                $query->execute($fields);

                return $query->rowCount() ? true : false;

            } catch (Exception $e) {
                echo "<script> alert('Error: ".$e->getMessage()."') </script>";
            }
        }
    }
?>
