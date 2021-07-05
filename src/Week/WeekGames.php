<?php
namespace Source\Week;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/general.php';

use Source\Club\Club;
use Source\Week\WeekService;

abstract class WeekGames
{
    protected $playerName;
    protected $season;
    protected $week;


    protected function __construct(string $playerName = "Results")
    {
        $this->playerName = $playerName;
    }


    protected function getWeekGames(string $competition, int $season, int $weekNum)
    {
        $this->season = $season;
        $weekService = new WeekService();

        $this->week = $weekService->getWeek($competition, $season, $weekNum);
        $this->week->setMatches(
            $weekService->weekGames($competition, $season, $this->week->getWeekNum(), $this->playerName)
        );
    }


    protected $currentMatchDay = null;
    protected function isMatchOnANewDay($match)
    {
        $kickOffDateTime = $match->getKickOff();
        $isNewDay = $kickOffDateTime->format("j") != $this->currentMatchDay;

        
        if ($isNewDay)
            $this->currentMatchDay = $kickOffDateTime->format("j");


        return $isNewDay;
    }


    protected function matchDayHTML($match)
    {
        $isFirstMatch = $match->getMatchNum() == 1;
        $day = $match->getKickOff()->format("l jS F");
        
        return 
            ($isFirstMatch ? "" : "</div>") . 
            "<div class='day'> <h3> $day </h3>";
    }


    protected function teamNameHTML(Club $team) 
    {
        $teamAbbreviateName = $team->getAbbreviate();
        $teamName = $team->getName();
        $teamFullName = $team->getFullName();

        return 
            "<p> 
                <span class='abbrView' style='display: none;'> $teamAbbreviateName </span>
                <span class='smallView' style='display: block;'> $teamName </span>
                <span class='largeView' style='display: none;'> $teamFullName </span>
            </p>";
    }
}
?>
