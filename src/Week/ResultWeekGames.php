<?php
namespace Source\Week;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/general.php';

use DateTime;

class ResultWeekGames extends WeekGames
{
    public function __construct() 
    {
        parent::__construct("Results");
    }


    public function gameWeekMatchesHTML(string $competition, int $season, int $weekNum = 0) 
    {
        $this->getWeekGames($competition, $season, $weekNum);

        $matchesHTML = $this->weekMatchesHTML($this->week->getMatches());

        return 
            "<div id='fixtures' style='width: 100%;'>
                $matchesHTML
            </div>";
    }


    private function weekMatchesHTML($weekMatches)
    {
        $matchesHTML = "";

        foreach ($weekMatches as $match)
        { 
            $isNewDay = $this->isMatchOnANewDay($match);
            if ($isNewDay) {
                $matchesHTML .= $this->matchDayHTML($match);
            }
            
            $matchesHTML .= $this->matchHTML($match);
        }

        return "$matchesHTML </div>";
    }

    
    private function matchHTML($match)
    {
        $gameNum = $match->getMatchNum();
        $homeTeamHTML = $this->teamNameHTML($match->getHomeTeam());
        $awayTeamHTML = $this->teamNameHTML($match->getAwayTeam());
        $matchScoreOrKickOffTimeHTML = $this->matchScoreOrKickOffTimeHTML($match);

        return 
            "<ul class='match game-$gameNum clear'>
                <li class='home'> $homeTeamHTML </li>
                $matchScoreOrKickOffTimeHTML
                <li class='away'> $awayTeamHTML </li>
            </ul>";
    }


    private function matchScoreOrKickOffTimeHTML($match)
    {
        $currentDateTime = new DateTime();
        $homeScore = $match->getHomeScore();
        $awayScore = $match->getAwayScore();

        $matchHasStarted = $match->getKickOff() < $currentDateTime;
        $matchHasAScore = $homeScore >= 0 && $awayScore >= 0;
        $matchHasFinished = $matchHasStarted && $matchHasAScore;

        if ($matchHasFinished) {
            return 
                "<li> <p> $homeScore </p> </li>
                <li> <p> - </p> </li>
                <li> <p> $awayScore </p> </li>";
        }
        else {
            return 
                "<li> </li>
                <li> <p> " . $match->getKickOff()->format("H:i") . " </p> </li>
                <li> </li>";
        }
    }
}
?>
