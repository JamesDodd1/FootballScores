<?php
namespace Source\Season;

use Database\Sql;
use DateTime;
use PDO;

class SeasonService
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
    public function getSeason(string $competition, int $season = 0): ?Season
    {
        $season = $this->sql->getSeasonFromDatabase($competition, $season);

        if (empty($season)) { return null; }

        return new Season(
            new DateTime($season[0]->StartDate), new DateTime($season[0]->EndDate)
        );
    }


    // Merge into getSeason()
    // Create getCurrentSeasonNum()
    // Ongoing season and finished season
    /**  */
    public function getCurrentSeason(string $competition)
    {
        $sql = "SELECT YEAR(s.`StartDate`) AS StartDate
                FROM `Season` s
                INNER JOIN `Competition`c
                    ON s.`CompetitionID` = c.`CompetitionID`
                WHERE c.`Name` = ? AND s.`EndDate` >= ?
                ORDER BY s.`EndDate` ASC
                LIMIT 1";

        return intval($this->crud->select($sql, [$competition, (new DateTime())->format('Y-m-d')])[0]->StartDate);
    }


    /**  */
    public function getTotalNumberOfWeeks(string $competition, int $season): int
    {
        $numOfWeeks = $this->sql->getTotalNumberOfWeeksFromDatabase($competition, $season);

        if (empty($numOfWeeks)) { return -1; }

        return $numOfWeeks[0]->NumOfWeeks;
    }
    

    /**  */
    public function getUsersSeasonScore(string $competition, int $season): ?array
    {
        $seasonScores = $this->sql->getUsersSeasonScoreFromDatabase($competition, $season);
        
        if (empty($seasonScores)) { return null; }

        return $seasonScores;
    }
}
?>
