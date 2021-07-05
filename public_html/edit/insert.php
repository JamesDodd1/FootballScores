<?php

$root = $_SERVER['DOCUMENT_ROOT'] . "/public_html";

require_once "$root/database/crud.php";
use FootballScores\Database\Crud;

include_once "$root/database/database.php";
$configs = include "$root/database/config.php";

$database = new FootballScores\Database\Database();
$database->connect($configs->host, $configs->username, $configs->password, $configs->database);
?>

<HTML>

<head>
    <style>
        body {
            padding: 5px;
        }
        select, input[type=datetime-local] {
            width: 200px;
        }
    </style>
</head>

<body>
    <?php
    $insert = new Insert($database->getConnection());
    
    echo $insert->generateHTML("Euros", 2021);
    ?>
</body>

</HTML>


<?php
class Insert
{
    private $crud;

    private $competition, $year;

    
    public function __construct(PDO $databaseConnection)
    {
        $this->crud = new Crud($databaseConnection);
    }


    public function generateHTML(string $competition, int $year)
    {
        $this->competition = $competition;
        $this->year = $year;

        $this->insertPostedGames();

        return 
            $this->numberOfGamesToInsertSelector(20) .
            $this->gamesToInsert();
    }


    private function numberOfGamesToInsertSelector(int $maxNumOfGames)
    {
        return
            "<form method='GET'>
                <select name='gameNum' onchange='this.form.submit()'>
                    <option value='0' disabled selected> SELECT </option>" .
                    $this->numberOfGamesToInsertSelectorOptions($maxNumOfGames) .
                "</select>
            </form>";
    }


    private function numberOfGamesToInsertSelectorOptions(int $maxNumOfGames)
    {
        $gameNum = isset($_GET['gameNum']) ? $_GET['gameNum'] : 0;
        $options = "";

        for ($i = 1; $i <= $maxNumOfGames; $i++) {
            if ($gameNum == $i) {
                $options .=
                    "<option value='$i' selected>" .
                        ($i == 1 ? "$i Game" : "$i Games") .
                    "</option>";
                
                continue;
            }

            $options .=
                "<option value='$i'>" .
                    ($i == 1 ? "$i Game" : "$i Games") .
                "</option>";
        }

        return $options;
    }


    private function gamesToInsert()
    {
        $numOfGames = isset($_GET['gameNum']) ? $_GET['gameNum'] : 0;

        if (!isset($numOfGames)) { return ""; }
        if ($numOfGames <= 0) { return ""; }

        return 
            "<form method='POST'>" .
                $this->allGamesToInsert($numOfGames) .
                "<input type='submit' name='insert' value='Insert'>
            </form>";
    }


    private function allGamesToInsert(int $numberOfGames)
    {
        $clubs = $this->getAllClubs($this->competition, $this->year);
        $gamesToInsert = "";

        for ($i = 1; $i <= $numberOfGames; $i++) {
            $gamesToInsert .= $this->singleGameToInsert($i, $clubs);
        }

        return $gamesToInsert;
    }


    private function getAllClubs($competition, $year)
    {
        $sql = "SELECT cl.`ClubID`, cl.`FullName`
                FROM `Club` cl
                INNER JOIN `SeasonClub` sc
                    ON cl.`ClubID` = sc.`ClubID`
                INNER JOIN `Season` s
                    ON sc.`SeasonID` = s.`SeasonID`
                INNER JOIN `Competition` c
                    ON s.`CompetitionID` = c.`CompetitionID`
                WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                ORDER BY cl.`FullName`";
        
        $clubs = $this->crud->select($sql, [$competition, $year]);

        $allClubs = [];
        foreach($clubs as $club) {
            $allClubs[] = array('ClubID' => $club->ClubID, 'Name' => $club->FullName);
        }

        return $allClubs;
    }


    private function singleGameToInsert(int $gameNumber, array $clubs)
    {
        return 
            "<h3> Game $gameNumber </h3>

            <pre> Home Team  " .
                $this->clubSelector("homeTeam-$gameNumber", $clubs) .
            "</pre>

            <pre> Away Team  " .
                $this->clubSelector("awayTeam-$gameNumber", $clubs) .
            "</pre>

            <pre> Start Time <input type='datetime-local' name='kickOff-$gameNumber'> </pre>
            
            <hr>";
    }


    private function clubSelector(string $name, array $clubs)
    {
        $options = "";
        foreach ($clubs as $club) {
            $clubID = $club['ClubID'];
            $clubName = $club['Name'];

            $options .= "<option value='$clubID'> $clubName </option>";
        }

        return 
            "<select name='$name'>
                <option value='0' disabled selected> SELECT </option>
                $options
            </select>";
    }
    
    

    private function areGamesInputValueValid()
    {
        $numberOfGames = $_GET['gameNum'];

        for ($i = 1; $i <= $numberOfGames; $i++) {
            $homeTeam = $_POST["homeTeam-$i"];
            $awayTeam = $_POST["awayTeam-$i"];
            $kickOff = date('Y-m-d H:i:s', strtotime($_POST["kickOff-$i"]));
            $todaysDate = (new DateTime("now", new DateTimeZone('Europe/London')))->format('Y-m-d H:i:s');
            
            if ($homeTeam == 0 || $awayTeam == 0 || $kickOff < $todaysDate) { return false;}
        }

        return true;
    }
    
    
    private function insertPostedGames()
    {
        if (!isset($_POST['insert'])) { return; }

        if (!$this->areGamesInputValueValid()) { echo "ERROR"; return; }


        if (!$this->insertNewWeekToDatabase()) { return; }
        if (!$this->insertNewWeekScoresToDatabase()) { return; }
        if (!$this->insertNewGamesToDatabase()) { return; }
        if (!$this->insertNewScoresToDatabase()) { return; }


        echo "Complete";
    }


    private function insertNewWeekToDatabase()
    {
        $finalGame = $_GET['gameNum'];
        $startDate = date('Y-m-d H:i:s', strtotime($_POST['kickOff-1']));
        $endDate = date('Y-m-d', strtotime($_POST["kickOff-$finalGame"]));

        $sql = "INSERT INTO `Week` (`SeasonID`, `WeekNum`, `StartTime`, `EndDate`)
                SELECT s.`SeasonID`, 
                    CASE
                        WHEN w.`WeekNum` IS NULL
                        THEN 1
                        ELSE w.`WeekNum` + 1
                    END AS WeekNum, $startDate, $endDate
                FROM `Week` w
                RIGHT JOIN `Season` s
                    ON w.`SeasonID` = s.`SeasonID`
                INNER JOIN `Competition` c
                    ON s.`CompetitionID` = c.`CompetitionID`
                WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                ORDER BY w.`WeekNum` DESC
                LIMIT 1";
        
        return $this->crud->insert($sql, [$this->competition, $this->year]);
    }


    private function insertNewWeekScoresToDatabase()
    {
        $sql = "INSERT INTO `WeekScore` (`UserID`, `WeekID`)
                SELECT `UserID`, (
                    SELECT w.`WeekID`
                    FROM `Week` w
                    INNER JOIN `Season` s
                        ON w.`SeasonID` = s.`SeasonID`
                    INNER JOIN `Competition` c
                        ON s.`CompetitionID` = c.`CompetitionID`
                    WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                    ORDER BY w.`WeekNum` DESC
                    LIMIT 1
                ) AS WeekID
                FROM `User`
                WHERE `Answers` = 0
                ORDER BY `UserID` ASC";

        return $this->crud->insert($sql, [$this->competition, $this->year]);
    }


    private function insertNewGamesToDatabase()
    {
        $numberOfGames = $_GET['gameNum'];

        for ($i = 1; $i <= $numberOfGames; $i++) {
            $homeTeam = $_POST["homeTeam-$i"];
            $awayTeam = $_POST["awayTeam-$i"];
            $kickOff = date('Y-m-d H:i:s', strtotime($_POST["kickOff-$i"]));

            $sql = "INSERT INTO `Game` (`WeekID`, `HomeTeam`, `AwayTeam`, `KickOff`)
                    SELECT w.`WeekID`, $homeTeam, $awayTeam, $kickOff
                    FROM `Week` w
                    INNER JOIN `Season` s
                        ON w.`SeasonID` = s.`SeasonID`
                    INNER JOIN `Competition` c
                        ON s.`CompetitionID` = c.`CompetitionID`
                    WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                    ORDER BY w.`WeekNum` DESC
                    LIMIT 1";
            
            $hasBeenInserted = $this->crud->insert($sql, [$this->competition, $this->year]);
            
            if (!$hasBeenInserted) { return false; }
        }

        return true;
    }


    private function insertNewScoresToDatabase()
    {
        $sql = "INSERT INTO `Score` (`GameID`, `UserID`)
                SELECT g.`GameID`, u.`UserID`
                FROM `Game` g
                INNER JOIN `Week` w
                    ON g.`WeekID` = w.`WeekID`
                INNER JOIN `WeekScore` ws
                    ON w.`WeekID` = ws.`WeekID`
                INNER JOIN `User` u
                    ON ws.`UserID` = u.`UserID`
                WHERE g.`WeekID` = (
                    SELECT w.`WeekID`
                    FROM `Week` w
                    INNER JOIN `Season` s
                        ON w.`SeasonID` = s.`SeasonID`
                    INNER JOIN `Competition` c
                        ON s.`CompetitionID` = s.`CompetitionID`
                    WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
                    ORDER BY w.`WeekNum` DESC
                    LIMIT 1
                )
                ORDER BY u.`UserID` ASC, g.`GameID` ASC";

        return $this->crud->insert($sql, [$this->competition, $this->year]);
    }
}
?>
