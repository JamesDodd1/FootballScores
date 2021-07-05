<?php
namespace Source\Table;

use Database\Sql;
use PDO;
use Source\Club\Club;

class TableService
{
    private $sql;

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


    public function getLeaderboard(string $competition, int $year) // TESTED
    {
        $leaderboard = $this->sql->getLeaderboardFromDatabase($competition, $year);

        if (empty($leaderboard)) { return null; }

        return $leaderboard;
    }


    /**  */
    public function getLeagueTable(string $competition, int $year)
    {
        $unorderedTables = $this->getAllClubsLeagueStats($competition, $year);
        return $this->orderLeagueTables($unorderedTables);
    }


    protected function getAllClubsLeagueStats($competition, $year)
    {
        $leagueTables = [];
        $positions = [];
        
        $clubsWithLeagueStats = $this->sql->getClubsStatsFromLeagueTableFromDatabase($competition, $year);
        
        
        $tableName = "";
        foreach ($clubsWithLeagueStats as $club)
        {
            if (is_null($club)) { continue; }
            
            $newLeagueTable = $tableName != "" && $club->TableName != $tableName;
            if ($newLeagueTable) {
                $leagueTables[] = (object) [
                    'Name' => $tableName,
                    'Positions' => $positions,
                ];
                
                $positions = [];
            }

            $tableName = $club->TableName;


            $positions[] = new Position(
                new Club($club->Name, $club->FullName, $club->Abbreviation),
                intval($club->Won),
                intval($club->Drawn),
                intval($club->Lost),
                intval($club->GoalsFor),
                intval($club->GoalsAgainst)
            );
        }

        $leagueTables[] = (object) [
            'Name' => $tableName,
            'Positions' => $positions,
        ];

        return $leagueTables;
    }

    protected function orderLeagueTables(array $leagueTables)
    {
        foreach ($leagueTables as $table) {
            usort($table->Positions, function(Position $club1, Position $club2) {
                if ($club1->getPoints() != $club2->getPoints()) {
                    return $club2->getPoints() - $club1->getPoints();
                }
                
                if ($club1->getGoalDifference() != $club2->getGoalDifference()) {
                    return $club2->getGoalDifference() - $club1->getGoalDifference();
                }
            
                if ($club1->getGoalsFor() != $club2->getGoalsFor()) {
                    return $club2->getGoalsFor() - $club1->getGoalsFor();
                }
            
                return strcmp($club1->getClub()->getFullName(), $club2->getClub()->getFullName());
            });
        }

        return $leagueTables;
    }
}
?>
