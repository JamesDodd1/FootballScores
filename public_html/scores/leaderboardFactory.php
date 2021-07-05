<?php

include_once "leaderboard.php";

class LeaderboardFactory
{
    public static function create(string $competition, int $year)
    {
        switch ($competition)
        {
            case "premier-league":
                return new Leaderboard("Premier League", $year);
            case "euros":
                return new Leaderboard("Euros", $year);
            default:
                return new Leaderboard("Premier League", $year);
        }
    }
}
?>