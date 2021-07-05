<?php
namespace Database;

use Exception;
use PDO;

class Crud
{
    /** @var PDO */
    protected $databaseConnection;

    public function __construct(PDO $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection; 
    } 


    /** 
     * @param string $sql Select query to get values from the database
     * @param array $parameters
     * @param int $fetchMethod
     * @return array
     */
    public function select(string $sql, array $parameters = [], $fetchMethod = PDO::FETCH_OBJ)
    {
        if (is_null($this->databaseConnection)) { throw new Exception("Database connection is not set"); }
        
        try {
            $query = $this->databaseConnection->prepare($sql);
            $query->execute($parameters);
            
            return $query->fetchAll($fetchMethod);
        } 
        catch (Exception $e) {
            //Log::write($e->getMessage());
            throw $e;
        }
    }


    /** 
     * @param string $sql Insert query to add values to the database
     * @param array $parameters
     * @return bool
     */
    public function insert(string $sql, array $parameters = [])
    {
        if (is_null($this->databaseConnection)) { throw new Exception("Database connection is not set"); }

        try {
            $query = $this->databaseConnection->prepare($sql);
            return $query->execute($parameters);
        } 
        catch (Exception $e) {
            //Log::write($e->getMessage());
            throw $e;
        }
    }


    /** 
     * @param string $sql Update query to change values in the database
     * @param array $parameters
     * @return bool
     */
    public function update(string $sql, array $parameters = [])
    {
        if (is_null($this->databaseConnection)) { throw new Exception("Database connection is not set"); }

        try {
            $query = $this->databaseConnection->prepare($sql);
            return $query->execute($parameters);
        } 
        catch (Exception $e) {
            //Log::write($e->getMessage());
            throw $e;
        }
    }


    /** 
     * @param string $sql Delete query to remove values from the database
     * @param array $parameters
     * @return bool
     */
    public function delete(string $sql, array $parameters = [])
    {
        if (is_null($this->databaseConnection)) { throw new Exception("Database connection is not set"); }

        try {
            $query = $this->databaseConnection->prepare($sql);
            return $query->execute($parameters);
        } 
        catch (Exception $e) {
            //Log::write($e->getMessage());
            throw $e;
        }
    }


    /** Runs SQL */
    protected function runSQL(string $sql, array $fields = [])
    {
        if (is_null($this->databaseConnection)) { return false; }


        try {
            $query = $this->databaseConnection->prepare($sql);
            return $query->execute($fields);

        } catch (Exception $e) {
            echo "<script> alert('Error: " . $e->getMessage() . "') </script>";
        }

        return false;
    }
}
?>
