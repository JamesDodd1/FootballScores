<?php

include_once "EurosGroupTable.php";
include_once "PremierLeagueTable.php";

class TableFactory
{
    public static function create(string $competition, int $year)
    {
        switch ($competition) {
            case "premier-league":
                return new PremierLeagueTable($year);
            case "euros":
                return new EurosGroupTable($year);
            default:
                return new PremierLeagueTable($year);
        }
    }
}

?>
