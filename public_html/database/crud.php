
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


        /** Run SQL and return rows in a multi dimentional array */
        private function getArrayObjectsSQL(string $sql, array $fields = []): ?array 
        {
            try {
                $query = $this->con->prepare($sql);
                $query->execute($fields);
                $rows = $query->fetchAll(PDO::FETCH_OBJ);
                
                // If no rows found 
                return ($rows == false) ? null : $rows;

            } catch (Exception $e) {
                //$this->errorMessage([$e]);

                return null;
            }
        }


        /** Runs SQL and return row in an array */
        private function getObjectSQL($sql, array $fields = []): ?object 
        {
            try {
                $query = $this->con->prepare($sql);
                $query->execute($fields);
                $row = $query->fetch(PDO::FETCH_OBJ);
                
                // If no row row found
                return ($row == false) ? null : $row;

            } catch (Exception $e) {
                //$this->errorMessage([$e]);

                return null;
            }
        }


        /** Runs SQL and return a field */
        private function getFieldSQL(string $sql, string $getField, array $fields = []): ?string
        {
            try {
                $query = $this->dbc->prepare($sql);
                $query->execute($fields);
                $row = $query->fetch(PDO::FETCH_OBJ);

                return $row->{$getField};

            } catch (Exception $e) {
                //$this->errorMessage([$e]);

                return null;
            }
        }


        /** Runs SQL and check if returned a result */
        private function doesExistSQL(string $sql, array $fields = []) 
        {
            try {
                $query = $this->con->prepare($sql);
                $row = $query->execute($fields);

                return ($query->rowCount()) ? true : false;

            } catch (Exception $e) {
                //$this->errorMessage([$e]);
                
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
