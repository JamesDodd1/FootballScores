<?php
namespace Source\Week;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/general.php';

use Database\Sql;
use DateTime;
use Exception;
use PDO;
use Source\Club\ClubService;
use Source\Game\Game;

class WeekService
{
    protected $sql, $clubService;

    public function __construct() 
    {
        $this->clubService = new ClubService();
        $this->sql = new Sql($this->databasePDO());
    }

    private function databasePDO()
    {
        $config = include DATABASE_CONFIG;

        $db = $config->connections->localhost;
        
        return new PDO(
            "mysql:host=$db->host; dbname=$db->database; charset=utf8",
            $db->username,
            $db->password
        );
    }


    /** 
     * Gets a list of all matches 
     * 
     * @param int $seasonStartYear Year the season started
     * @param int $weekNum Week number to display
     * @param int $user Name of the user
     * 
     * @return array|null Array contains Match objects
     */
    public function weekGames(string $competition, int $seasonStartYear, int $weekNum, string $user): ?array // TESTED
    {
        $weekGames = $this->sql->getWeekGamesFromDatabase($competition, $seasonStartYear, $weekNum, $user);
        
        if (empty($weekGames)) { return null; }

        $matchNum = 0;
        $matches = [];
        foreach ($weekGames as $game)
        {
            $matches[] = new Game(
                ++$matchNum,
                $this->clubService->getClub($game->HomeTeam),
                $this->clubService->getClub($game->AwayTeam),
                $game->HomeScore,
                $game->AwayScore,
                new DateTime($game->KickOff),
                $game->Win,
                $game->Draw,
                $game->Lose,
                $game->Joker
            );
        }

        return $matches;
    }


    /**  */
    public function getCurrentWeekNum(string $competition, int $season): int // TESTED
    {
        $week = $this->sql->getOngoingOrNextWeekNumFromDatabase($competition, $season);
        if (!empty($week)) { return intval($week[0]->WeekNum); }

        $week = $this->sql->getPreviousWeekNumFromDatabase($competition, $season);
        if (!empty($week)) { return intval($week[0]->WeekNum); }

        return -1;
    }


    /**  */
    public function getWeek(string $competition, int $seasonStartYear, int $weekNum = 0): ?Week // TESTED
    {
        if ($weekNum < 0) { throw new Exception('Method argument $weekNum cannot be a negative number'); }

        if ($weekNum == 0) {
            $weekNum = $this->getCurrentWeekNum($competition, $seasonStartYear);

            if ($weekNum == -1) { return null; }
        }

        $week = $this->sql->getWeekFromDatabase($competition, $seasonStartYear, $weekNum);

        if (count($week) == 0) { return null; }

        return new Week(
            intval($week[0]->WeekNum),
            new DateTime($week[0]->StartTime),
            new DateTime($week[0]->EndDate)
        );
    }


    /**  */
    public function getAllUsersWeekScores(string $competition, int $season): ?array // TESTED
    {
        $allUsersScores = $this->sql->getAllUsersWeekScoresFromDatabase($competition, $season);

        if (empty($allUsersScores)) { return null; }

        return $allUsersScores;

        /*
        $scores = [];
        $week = [];
        $weekNum = 0;
        foreach ($allUsersWeekScores as $weekScore) {

        }
        for ($i = 0; $i <= count($rows); $i++)
        {
            // End of rows count
            if ($i == count($rows))
            {
                $scores[] = $week;
                break;
            }

            // If new week
            if ($rows[$i]->WeekNum != $weekNum)
            {
                // If first iteration
                if ($i == 0)
                    $scores = [];
                else 
                    $scores[] = $week;

                // Create new week
                $weekNum = $rows[$i]->WeekNum;
                $week = [];
                $week[] = $weekNum;
            }

            // Add scores
            $week[] = $rows[$i]->Score;
        }

        return $scores;
        */
    }


    /**  */
    public function getUsersWeekScore(string $user, string $competition, int $seasonStart, int $weekNum = 0) // TESTED
    {
        if ($weekNum < 0) { throw new Exception('Method argument $weekNum cannot be a negative number'); }

        if ($weekNum == 0) {
            $weekNum = $this->getCurrentWeekNum($competition, $seasonStart);

            if ($weekNum == -1) { return -1; }
        }

        $weekScores = $this->sql->getUsersWeekScoreFromDatabase($competition, $seasonStart, $weekNum, $user);

        if (empty($weekScores)) { return -1; }
        
        return intval($weekScores[0]->Score);
    }
}
?>
