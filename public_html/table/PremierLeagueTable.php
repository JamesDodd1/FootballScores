<?php

$root = $_SERVER['DOCUMENT_ROOT'] . "/public_html";
include_once "$root/table/LeagueTableInterface.php";
include_once "$root/table/TableHTML.php";
include_once "$root/database/sql.php";

class PremierLeagueTable implements LeagueTable
{
    private $year;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function generateHTML()
    {
        global $database;

        $db = new Sql($database->getConnection());

        $leagueTable = $db->getLeagueTable("Premier League", $this->year);

        $premierLeague = new PremierLeague();
        $premierLeague->setPositions($leagueTable[0]->Positions);
        
        $tableHTML = new TableHTML($premierLeague);
        
        return $tableHTML->generate();
    }
}

?>
