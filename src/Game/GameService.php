<?php
namespace Source\Game;

use Database\Sql;
use Exception;
use PDO;

class GameService
{
    protected $sql;

    public function __construct() 
    {
        $this->sql = new Sql($this->databasePDO());
    }

    private function databasePDO()
    {
        $config = include 'config/database.php';

        $db = $config->connections->localhost;
        
        return new PDO(
            "mysql:host=$db->host; dbname=$db->database; charset=utf8",
            $db->username,
            $db->password
        );
    }


    /**  */
    public function setScores(string $user, int $seasonStart, int $weekNum, Game $match,
        int $homeScore, int $awayScore, bool $joker = false) // TESTED
    {
        if ($weekNum <= 0) { throw new Exception('Method argument $weekNum cannot be zero or a negative number'); }

        return $this->sql->setScoresInDatabase(
            $user, $seasonStart, $weekNum, $match, $homeScore, $awayScore, $joker
        );
    }
}
?>
