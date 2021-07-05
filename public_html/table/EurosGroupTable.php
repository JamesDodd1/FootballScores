<?php

$root = $_SERVER['DOCUMENT_ROOT'] . "/public_html";
include_once "$root/table/LeagueTableInterface.php";
include_once "$root/table/TableHTML.php";

class EurosGroupTable implements LeagueTable
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

        $leagueTables = $db->getLeagueTable("Euros", $this->year);

        $groupsHTML = "";
        foreach($leagueTables as $group) {
            $euroGroup = new EuroGroup($group->Name);
            $euroGroup->setPositions($group->Positions);

            $tableHTML = new TableHTML($euroGroup);
            $groupsHTML .= $tableHTML->generate() . "<br>";
        }
        
        return $groupsHTML;
    }
}

?>
