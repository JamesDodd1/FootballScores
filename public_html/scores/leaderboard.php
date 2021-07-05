<?php

$root = $_SERVER['DOCUMENT_ROOT'] . "/public_html";
include_once "$root/database/sql.php";

class Leaderboard
{
    private $competition, $year;

    public function __construct(string $competition, int $year)
    {
        $this->competition = $competition;
        $this->year = $year;
    }

    public function generateHTML()
    {
        return 
            "<div id='leaderboard'>
                <h2> Leaderboard </h2>
                <hr />" .
                $this->table() .
            "</div>";
    }

    private function table()
    {
        return 
            "<table>" .
                $this->tableHeaders() .
                $this->tableContent() .
            "</table>";
    }

    private function tableHeaders()
    {
        return 
            "<tr class='header'>
                <th> </th>
                <th colspan='2'> <b> Name </b> </th>
                <th> <b> Pts </b> </th>
            </tr>";
    }

    private function tableContent()
    {
        global $database;

        $db = new Sql($database->getConnection());
        $players = $db->leaderboard($this->competition, $this->year);
        
        $tableContent = "";
        for ($i = 1; $i <= count($players); $i++) {
            $tableContent .= $this->tableRow($i, $players[$i - 1]);
        }

        return $tableContent;
    }

    private function tableRow($position, $player)
    {
        return 
            "<tr class='player'>
                <td class='pos'> <p> $position </p> </td>
                <td class='name'> <p> $player->Name </p> </td>
                <td class='weekPts'> <p> +" . intval($player->WeekScore) . "</p> </td>
                <td class='totalPts'> <p>" . intval($player->SeasonScore) . "</p> </td>
            </tr>";
    }
}
?>
