<?php
namespace Database;

use DateTime;
use PDO;
use Source\Game\Game;

class Sql
{
    private $crud;

    public function __construct(PDO $databaseConnection)
    { 
        $this->crud = new Crud($databaseConnection);
    }


    public function getAllUsersFromDatabase()
    {
        $sql = "SELECT `Name`, `Answers`
                FROM `User`
                WHERE `Answers` = 0
                ORDER BY `Name` ASC";

        return $this->crud->select($sql);
    }


    public function getUserFromDatabase(string $user)
    {
        $sql = "SELECT `Name`, `Answers`
                FROM `User`
                WHERE `Name` = ?
                LIMIT 1";
        
        return $this->crud->select($sql, [$user]);
    }


    public function getWeekGamesFromDatabase(string $competition, int $season, int $weekNum, string $user): array
    {
        $sql = "SELECT ch.`FullName` AS HomeTeam, ca.`FullName` AS AwayTeam, sc.`HomeScore`, sc.`AwayScore`,
                    g.`KickOff`, sc.`Win`, sc.`Draw`, sc.`Lose`, sc.`Joker`
                FROM `Competition` c
                INNER JOIN `Season` s 
                    ON c.`CompetitionID` = s.`CompetitionID`
                INNER JOIN `Week` w
                    ON s.`SeasonID` = w.`SeasonID` 
                INNER JOIN `Game` g
                    ON w.`WeekID` = g.`WeekID` 
                INNER JOIN `Score` sc
                    ON g.`GameID` = sc.`GameID` 
                INNER JOIN `Club` ch
                    ON g.`HomeTeam` = ch.`ClubID` 
                INNER JOIN `Club` ca
                    ON g.`AwayTeam` = ca.`ClubID` 
                INNER JOIN `User` u
                    ON sc.`UserID` = u.`UserID` 
                WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ? AND w.`WeekNum` = ?
                    AND u.`Name` = ? AND g.`Postponed` = 0
                ORDER BY `KickOff` ASC, ch.`FullName` ASC;";

        return $this->crud->select($sql, [$competition, $season, $weekNum, $user]);
    }


    public function getOngoingOrNextWeekNumFromDatabase(string $competition, int $season)
    {
        $sql = "SELECT w.`WeekNum`
                FROM `Week` w 
                INNER JOIN `Season` s
                    ON w.`SeasonID` = s.`SeasonID`
                INNER JOIN `Competition` c
                    ON s.`CompetitionID` = c.`CompetitionID`
                WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                    AND DATE(w.`EndDate`) >= ? 
                ORDER BY w.`EndDate` ASC
                LIMIT 1;";

        return $this->crud->select($sql, [$competition, $season, (new DateTime())->format('Y-m-d')]);
    }


    public function getPreviousWeekNumFromDatabase(string $competition, int $season)
    {
        $sql = "SELECT w.`WeekNum`
                FROM `Week` w
                INNER JOIN `Season` s
                    ON w.`SeasonID` = s.`SeasonID`
                INNER JOIN `Competition` c
                    ON s.`CompetitionID` = c.`CompetitionID`
                WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                    AND DATE(w.`EndDate`) < ?
                ORDER BY w.`EndDate` DESC
                LIMIT 1";

        return $this->crud->select($sql, [$competition, $season, (new DateTime())->format('Y-m-d')]);
    }


    public function getWeekFromDatabase(string $competition, int $season, int $weekNum)
    {
        $sql = "SELECT w.`WeekNum`, w.`StartTime`, w.`EndDate`
                FROM `Week` w
                INNER JOIN `Season` s
                    ON w.`SeasonID` = s.`SeasonID`
                INNER JOIN `Competition` c
                    ON s.`CompetitionID` = c.`CompetitionID`
                WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                    AND w.`WeekNum` = ?
                LIMIT 1";

        return $this->crud->select($sql, [$competition, $season, $weekNum]);
    }


    public function getUsersWeekScoreFromDatabase(string $competition, int $seasonStart, int $weekNum, string $user)
    {
        $sql = "SELECT ws.`Score`
                FROM `Competition` c
                INNER JOIN `Season` s
                    ON c.`CompetitionID` = s.`CompetitionID`
                INNER JOIN `Week` w
                    ON s.`SeasonID` = w.`SeasonID`
                INNER JOIN `WeekScore` ws
                    ON w.`WeekID` = ws.`WeekID`
                INNER JOIN `User` u
                    ON ws.`UserID` = u.`UserID`
                WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                    AND w.`WeekNum` = ? AND u.Name = ?;";
        
        return $this->crud->select($sql, [$competition, $seasonStart, $weekNum, $user]);
    }


    public function setScoresInDatabase(string $user, int $seasonStart, int $weekNum, Game $match,
        int $homeScore, int $awayScore, bool $joker)
    {
        $sql = "UPDATE `Score` 
                SET `HomeScore` = ?, `AwayScore` = ?, `Joker` = ?
                WHERE `GameID` = (
                    SELECT g.`GameID`
                    FROM `Game` g 
                    WHERE g.`WeekID` = (
                        SELECT w.`WeekID`
                        FROM `Week` w
                        INNER JOIN `Season` s
                            ON w.`SeasonID` = s.`SeasonID` 
                        WHERE w.`WeekNum` = ? AND YEAR(s.`StartDate`) = ?
                    ) 
                    AND g.`HomeTeam` = (
                        SELECT c.`ClubID`
                        FROM `Club` c
                        WHERE c.`Name` = ?
                    ) 
                    AND g.`AwayTeam` = (
                        SELECT c.`ClubID`
                        FROM `Club` c
                        WHERE c.`Name` = ?
                    )
                ) 
                AND `UserID` = (
                    SELECT u.`UserID`
                    FROM `User` u
                    WHERE u.`Name` = ?
                )";

        return $this->crud->update($sql, [
            $homeScore,
            $awayScore,
            (int)$joker,
            $weekNum,
            $seasonStart,
            $match->getHomeTeam()->getName(),
            $match->getAwayTeam()->getName(),
            $user
        ]);
    }


    public function getAllUsersWeekScoresFromDatabase(string $competition, int $season)
    {
        $columns = [];
        foreach ($this->getAllUsersFromDatabase() as $user) {
            $name = $user->Name;
            $columns[] = "MAX(CASE WHEN u.`Name` = '$name' THEN ws.`Score` END) AS $name";
        }

        $userColumns = implode(', ', $columns);

        
        $sql = "SELECT w.`WeekNum`, $userColumns
                FROM `Competition` c
                INNER JOIN `Season` s
                    ON c.`CompetitionID` = s.`CompetitionID`
                INNER JOIN `Week` w
                    ON s.`SeasonID` = w.`SeasonID`
                INNER JOIN `WeekScore` ws
                    ON w.`WeekID` = ws.`WeekID`
                INNER JOIN `User` u
                    ON ws.`UserID` = u.`UserID` 
                WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                    AND w.`StartTime` <= ?
                GROUP BY w.`WeekNum`
                ORDER BY w.`WeekNum` ASC, u.`UserID` ASC;";

        return $this->crud->select($sql, [$competition, $season, (new DateTime())->format('Y-m-d H:i')]);



        // OLD SQL NEW ONE CURRENTLY UNTESTED
        $sql = "SELECT w.`WeekNum`, ws.`Score`
                FROM `Competition` c
                INNER JOIN `Season` s
                    ON c.`CompetitionID` = s.`CompetitionID`
                INNER JOIN `Week` w
                    ON s.`SeasonID` = w.`SeasonID`
                INNER JOIN `WeekScore` ws
                    ON w.`WeekID` = ws.`WeekID`
                INNER JOIN `User` u
                    ON ws.`UserID` = u.`UserID` 
                WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                    AND w.`StartTime` <= ?
                ORDER BY w.`WeekNum` ASC, u.`UserID` ASC";
        
        return $this->crud->select($sql, [$competition, $season, (new DateTime())->format('Y-m-d H:i')]);
    }


    public function getTotalNumberOfWeeksFromDatabase(string $competition, int $season)
    {
        $sql = "SELECT COUNT(w.`WeekNum`) AS NumOfWeeks
                FROM `Week` w
                INNER JOIN `Season` s
                    ON w.`SeasonID` = s.`SeasonID`
                INNER JOIN `Competition` c
                    ON s.`CompetitionID` = c.`CompetitionID`
                WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?";

        return $this->crud->select($sql, [$competition, $season]);
    }


    public function getUsersSeasonScoreFromDatabase(string $competition, int $season)
    {
        $columns = [];
        foreach ($this->getAllUsersFromDatabase() as $user) {
            $name = $user->Name;
            $columns[] = "MAX(CASE WHEN u.`Name` = '$name' THEN ss.`Score` END) AS $name";
        }

        $userColumns = implode(', ', $columns);
        
        $sql = "SELECT u.`Name`, $userColumns
                FROM `User` u
                INNER JOIN `SeasonScore` ss
                    ON u.`UserID` = ss.`UserID`
                INNER JOIN `Season` s
                    ON ss.`SeasonID` = s.`SeasonID`
                INNER JOIN `Competition` c
                    ON s.`CompetitionID` = c.`CompetitionID`
                WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                GROUP BY u.`Name`
                ORDER BY u.`UserID` ASC";
                
        return $this->crud->select($sql, [$competition, $season]);
    }


    public function getLeaderboardFromDatabase(string $competition, int $year)
    {
        $sql = "SELECT u.`Name`, s.`SeasonScore`, s.`WeekScore`
                FROM `User` u
                LEFT JOIN (
                    SELECT ss.`UserID`, ss.`Score` AS SeasonScore, ws.`Score` AS WeekScore, w.WeekNum
                    FROM `Competition` c
                    INNER JOIN `Season` s
                        ON c.`CompetitionID` = s.`CompetitionID`
                    INNER JOIN `SeasonScore` ss
                        ON s.`SeasonID` = ss.`SeasonID`
                    LEFT JOIN `Week` w
                        ON s.`SeasonID` = w.`SeasonID`
                    LEFT JOIN `WeekScore` ws
                        ON w.`WeekID` = ws.`WeekID` AND ss.`UserID` = ws.`UserID`
                    WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                    	AND DATE(w.`StartTime`) <= ?
                    ORDER BY w.`WeekNum` DESC
                    LIMIT 5
                ) s
                    ON u.`UserID` = s.`UserID`
                WHERE u.`Answers` = 0
                ORDER BY s.`SeasonScore` DESC, s.`WeekScore` DESC, u.`Name` ASC;";
                
        return $this->crud->select($sql, [$competition, $year, (new DateTime())->format('Y-m-d')]);
    }


    public function getClubsStatsFromLeagueTableFromDatabase($competition, $year)
    {
        $clubs = $this->getAllClubsFromDatabase($competition, $year);
        $questionMarksForNumOfClubs = str_repeat('?, ', count($clubs) - 1) . '?';
        $totalNumOfGames = $this->getTotalNumOfGamesForCompetitionFromDatabase($competition);
    
        
        $sql = "SELECT lt.`Name`, lt.`FullName`, lt.`Abbreviation`, lt.`TableName`,
                    t.`Won`, t.`Drawn`, t.`Lost`, t.`GoalsFor`, t.`GoalsAgainst`
                FROM (
                    SELECT cl.`ClubID`, cl.`Name`, cl.`FullName`, cl.`Abbreviation`, gt.`Name` AS TableName
                    FROM `GroupTable` gt
                    INNER JOIN `SeasonClub` sc
                        ON gt.`GroupTableID` = sc.`GroupTableID`
                    INNER JOIN `Club` cl
                        ON sc.`ClubID` = cl.`ClubID`
                    INNER JOIN `Season` s
                        ON sc.`SeasonID` = s.`SeasonID`
                    INNER JOIN `Competition` c
                        ON s.`CompetitionID` = c.`CompetitionID`
                    WHERE cl.`FullName` IN ($questionMarksForNumOfClubs) AND c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                ) lt

                LEFT JOIN (
                    SELECT lt.`ClubID`,
                        SUM(
                            CASE
                                WHEN (lt.`HomeScore` > lt.`AwayScore` AND lt.`ClubID` = lt.`HomeTeam`) 
                                    OR (lt.`AwayScore` > lt.`HomeScore` AND lt.`ClubID` = lt.`AwayTeam`)
                                THEN 1
                                ELSE 0
                            END
                        ) AS Won,

                        SUM(
                            CASE
                                WHEN lt.`HomeScore` = lt.`AwayScore`
                                    AND (lt.`ClubID` = lt.`HomeTeam` OR lt.`ClubID` = lt.`AwayTeam`)
                                THEN 1
                                ELSE 0
                            END
                        ) AS Drawn,

                        SUM(
                            CASE
                                WHEN (lt.`HomeScore` < lt.`AwayScore` AND lt.`ClubID` = lt.`HomeTeam`)
                                    OR (lt.`AwayScore` < lt.`HomeScore` AND lt.`ClubID` = lt.`AwayTeam`)
                                THEN 1
                                ELSE 0
                            END
                        ) AS Lost,

                        SUM(
                            CASE
                                WHEN lt.`HomeTeam` = lt.`ClubID`
                                THEN lt.`HomeScore`
                                ELSE lt.`AwayScore`
                            END
                        ) AS GoalsFor,

                        SUM(
                            CASE
                                WHEN lt.AwayTeam = lt.ClubID
                                THEN lt.HomeScore
                                ELSE lt.AwayScore
                            END
                        ) AS GoalsAgainst

                    FROM (
                        SELECT c.`ClubID`, c.`FullName`, g.`HomeTeam`, g.`AwayTeam`, sc.`HomeScore`, sc.`AwayScore`
                        
                        FROM `Club` c

                        INNER JOIN `Game` g
                            ON c.`ClubID` = g.`HomeTeam` OR c.`ClubID` = g.`AwayTeam`

                        INNER JOIN `Score` sc
                            ON g.`GameID` = sc.`GameID`

                        INNER JOIN `Week` w
                            ON g.`WeekID` = w.`WeekID`

                        INNER JOIN `Season` s
                            ON w.`SeasonID` = s.`SeasonID`

                        INNER JOIN `Competition` co
                            ON s.`CompetitionID` = co.`CompetitionID`
                        
                        INNER JOIN `LeagueTable` lt
                            ON co.`CompetitionID` = lt.`LeagueTableID`

                        WHERE c.`FullName` IN ($questionMarksForNumOfClubs)
                            AND `UserID` = (
                                SELECT u.`UserID`
                                FROM `User` u
                                WHERE u.`Answers` = 1
                            )
                            AND sc.`HomeScore` >= 0
                            AND sc.`AwayScore` >= 0
                            AND co.`Name` = ?
                            AND YEAR(s.`StartDate`) = ?
                        LIMIT $totalNumOfGames
                    ) lt
                    GROUP BY lt.`FullName`
                    ORDER BY lt.`FullName` ASC
                ) t
                ON lt.ClubID = t.ClubID";

        $clubNames = [];
        foreach ($clubs as $club) {
            $clubNames[] = $club->FullName;
        }


        return $this->crud->select($sql, array_merge(
            $clubNames, [$competition, $year], $clubNames, [$competition, $year]
        ));
    }


    public function getAllClubsFromDatabase(string $competition, int $year)
    {
        $sql = "SELECT cl.`Name`, cl.`FullName`, cl.`Abbreviation`
                FROM `Club` cl
                INNER JOIN `SeasonClub` sc
                    ON cl.`ClubID` = sc.`ClubID`
                INNER JOIN `Season` s
                    ON sc.`SeasonID` = s.`SeasonID`
                INNER JOIN `Competition` co
                    ON s.`CompetitionID` = co.`CompetitionID`
                WHERE co.`Name` = ? AND YEAR(s.`StartDate`) = ?
                ORDER BY cl.`FullName` ASC";

        return $this->crud->select($sql, [$competition, $year]);
    }


    public function getTotalNumOfGamesForCompetitionFromDatabase($competition)
    {
        $sql = "SELECT COUNT(gt.`LeagueTableID`) *
                    CASE
                        WHEN lt.`HomeAndAway` = 1
                        THEN (lt.`NumberOfTeams` - 1) * lt.`NumberOfTeams` * 2
                        ELSE (lt.`NumberOfTeams` - 1) * lt.`NumberOfTeams`
                    END AS TotalNumOfGames
                FROM `LeagueTable` lt
                INNER JOIN `GroupTable` gt
                    ON lt.`LeagueTableID` = gt.`LeagueTableID`
                INNER JOIN `Competition` c
                    ON lt.`CompetitionID` = c.`CompetitionID`
                WHERE c.`Name` = ?";

        return intval($this->crud->select($sql, [$competition])[0]->TotalNumOfGames);
    }


    public function getClubFromDatabase(string $fullName)
    {
        $sql = "SELECT `Name`, `FullName`, `Abbreviation`
                FROM `Club`
                WHERE `Relegated` = 0
                AND FullName = ?";
        
        return $this->crud->select($sql, [$fullName]);
    }


    public function getSeasonFromDatabase(string $competition, int $season)
    {
        $sql = "SELECT s.`StartDate`, s.`EndDate`
                FROM `Season` s
                INNER JOIN `Competition`c
                    ON s.`CompetitionID` = c.`CompetitionID`
                WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?";

        return $this->crud->select($sql, [$competition, $season]);
    }


    public function getClubFormFromDatabase(string $fullName, DateTime $from, int $numOfGames = 5)
    {
        $sql = "SELECT ch.`Abbreviation` AS HomeAbbr, ch.`Name` AS HomeName, ch.`FullName` AS HomeFull,
                    s.`HomeScore`, ca.`Abbreviation` AS AwayAbbr, ca.`Name` AS AwayName,
                    ca.`FullName` AS AwayFull, s.`AwayScore`
                FROM Score s 
                INNER JOIN `Game` g
                    ON s.`GameID` = g.`GameID`
                INNER JOIN `Club` ch
                    ON g.`HomeTeam` = ch.`ClubID`
                INNER JOIN `Club` ca
                    ON g.`AwayTeam` = ca.`ClubID`
                WHERE (ch.`FullName` = ? OR ca.`FullName` = ?)
                    AND g.`KickOff` < IF(? > ?, ?, ?) 
                    AND g.`Postponed` = 0 AND s.`UserID` = 1
                ORDER BY g.`KickOff` DESC
                LIMIT $numOfGames";

        return $this->crud->select($sql, [
            $fullName,
            $fullName,
            $from->format("Y-m-d H:i"),
            (new DateTime())->format("Y-m-d H:i"),
            (new DateTime())->format("Y-m-d H:i"),
            $from->format("Y-m-d H:i"),
            //$numOfGames,
        ]);
    }
}

?>
