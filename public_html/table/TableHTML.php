
<script type="text/javascript" src="/table/tableDisplay.js"></script>

<?php

$root = $_SERVER['DOCUMENT_ROOT'] . "/public_html";

include_once "$root/database/database.php";
$configs = include "$root/database/config.php";

$database = new FootballScores\Database\Database();
$database->connect($configs->host, $configs->username, $configs->password, $configs->database);



class TableHTML
{
    private $leagueTable;

    public function __construct($leagueTable)
    {
        $this->leagueTable = $leagueTable;
    }
    
    public function generate()
    {
        return
            "<div class='leagueTable'>
                <h2>" . $this->leagueTable->getName() . "</h2>
                <hr />" .
                $this->table() .
            "</div>
            <script> updateTable(); </script>";
    }

    private function table()
    {
        return
            "<table class='table'>" .
                $this->tableColumnHeaders() .
                $this->tableContent() .
            "</table>";
    }

    private function tableColumnHeaders()
    {
        return 
            "<tr class='header'> " .
                $this->columnHeader("pos", "", "Pos", "Position") .
                "<th class='team'>
                    <b> Team </b>
                </th>" .
                $this->columnHeader("play", "P", "Pos", "Position") .
                $this->columnHeader("won", "W", "Won", "Won") .
                $this->columnHeader("draw", "D", "Drawn", "Drawn") .
                $this->columnHeader("lost", "L", "Lost", "Lost") .
                $this->columnHeader("for", "GF", "For", "Goals For") .
                $this->columnHeader("agst", "GA", "Against", "Goals Against") .
                $this->columnHeader("dif", "GD", "Difference", "Goals Difference") .
                $this->columnHeader("pts", "Pts", "Points", "Points") .
            "</tr>";
    }

    private function columnHeader(string $class, string $abbreviatedName, string $shortenedName, string $fullName)
    {
        return 
            "<th class='$class'>
                <b>
                    <span class='abbr'> $abbreviatedName </span>
                    <span class='norm'> $shortenedName </span>
                    <span class='full'> $fullName </span>
                </b>
            </th>";
    }

    private function tableContent()
    {
        $rows = "";
        
        $positions = $this->leagueTable->getPositions();
        //$cl_Pos = $this->leagueTable->getChampionsLeaguePos();
        //$rel_Pos = $this->leagueTable->getRelegationPos();

        $pos = 1;
        foreach ($positions as $position)
        {
            //$line = ($pos == $cl_Pos || $pos == $rel_Pos) ? " line" : null;

            $rows .= $this->rowContent($pos++, $position, $line = "");
        }

        return $rows;
    }

    private function rowContent($leaguePosNum, $position, $line)
    {
        $goalDif = $position->getGoalDifference();

        return
            "<tr class='row $line'> " .
                $this->cellContent("pos", $leaguePosNum) .
                "<td class='team'> 
                    <b>
                        <span class='abbr'>" . $position->getClub()->getAbbreviate() . "</span>
                        <span class='norm'>" . $position->getClub()->getName() . "</span>
                        <span class='full'>" . $position->getClub()->getFullName() . "</span>
                    </b> 
                </td>" .
                $this->cellContent("play", $position->getPlayed()) .
                $this->cellContent("won", $position->getWon()) .
                $this->cellContent("draw", $position->getDrawn()) .
                $this->cellContent("lost", $position->getLost()) .
                $this->cellContent("for", $position->getGoalsFor()) .
                $this->cellContent("agst", $position->getGoalsAgainst()) .
                $this->cellContent("dif", ($goalDif > 0 ? "+$goalDif" : $goalDif)) .
                $this->cellContent("pts", $position->getPoints()) .
            "</tr>";
    }

    private function cellContent(string $class, $content)
    {
        return 
            "<td class='$class'> 
                <p> $content </p> 
            </td>";
    }
}
?>
