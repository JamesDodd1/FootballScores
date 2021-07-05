<?php
namespace Source\Week;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/general.php';

class WeekGamesFactory
{
    public static function create(string $competition, int $year, string $user = null)
    {
        switch ($competition)
        {
            case "premier-league":
                if (empty($user)) { return new ResultWeekGames(); }

                return new PlayerWeekGames($user);
            case "euros":
                return;
            default:
                return;
        }
    }
}
?>
