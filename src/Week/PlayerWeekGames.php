<?php
namespace Source\Week;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/general.php';

use DateTime;
use Source\Club\Club;
use Source\Club\ClubService;
use Source\Game\GameService;

class PlayerWeekGames extends WeekGames
{
    private $isScoresSet;

    public function __construct(string $playerName) 
    {
        parent::__construct($playerName);
    }


    public function gameWeekMatchesHTML(string $competition, int $season, int $weekNum = 0) 
    {
        $this->getWeekGames($competition, $season, $weekNum);

        $this->isScoresSet = $this->scoresComplete($this->week);
        $this->submitClicked($this->week);

        $matchesHTML = $this->weekMatchesHTML($this->week->getMatches());
        $saveOrEditButtonHTML = $this->saveOrEditButtonHTML();


        return 
            "<div id='fixtures' style='width: 100%;'>
                <form method='POST'>
                    $matchesHTML
                    $saveOrEditButtonHTML
                </form>
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
        $predictionResult = $this->predictedScoreResult($match);

        $formArrowHTML = $this->formArrowHTML($gameNum);
        $homeTeamHTML = $this->teamNameHTML($match->getHomeTeam());
        $awayTeamHTML = $this->teamNameHTML($match->getAwayTeam());
        $homeTeamFormHTML = $this->teamFormHTML($match, $match->getHomeTeam());
        $awayTeamFormHTML = $this->teamFormHTML($match, $match->getAwayTeam());
        $matchScoreOrKickOffTimeHTML = $this->matchScoreOrKickOffTimeHTML($match);
        $jokerHTML = $this->jokerHTML($match);


        return 
            "<ul class='match $predictionResult game-$gameNum clear'>
                $formArrowHTML
                <li class='home' onclick='toggleForm($gameNum)'> 
                    $homeTeamHTML 
                    $homeTeamFormHTML
                </li>
                $matchScoreOrKickOffTimeHTML
                <li class='away' onclick='toggleForm($gameNum)'>
                    $awayTeamHTML
                    $awayTeamFormHTML
                </li>
                $jokerHTML
            </ul>";
    }


    private function predictedScoreResult($match)
    {
        if ($match->getWin()) 
            return "win";
        else if ($match->getDraw()) 
            return "draw";
        else if ($match->getLose()) 
            return "lose";
        
        return "";
    }


    private function formArrowHTML(int $gameNum) 
    {
        return 
            "<li class='form-arrow' onclick='toggleForm($gameNum)'>
                <img class='close' src='/images/downArrow.png' alt='Down arrow' />
            </li>";
    }


    private function teamFormHTML($match, Club $team)
    {
        $clubFullName = $team->getFullName();
        $clubForm = (new ClubService())->getClubsForm($clubFullName, $match->getKickOff());

        if (is_null($clubForm)) { return ""; }


        $gameNum = $match->getMatchNum();
        $isPlayingAtHome = $team == $match->getHomeTeam();
        $formMatchesHTML = $this->formMatchesHTML($team, $clubForm, $isPlayingAtHome);

        return 
            "<div class='recent clubForm-$gameNum' style='display: none;'>
                $formMatchesHTML
            </div>";
    }


    private function formMatchesHTML(Club $team, $clubForm, $isPlayingAtHome)
    {
        $formMatchesHTML = "";

        foreach ($clubForm as $previousMatch) {
            $isFormMatchAtHome = $team->getFullName() == $previousMatch->HomeFull;

            $formMatch = $this->formMatchInfo($isFormMatchAtHome, $previousMatch);
            $form = $isPlayingAtHome ? $this->homeTeamFormHTML($formMatch) : $this->awayTeamFormHTML($formMatch);

            $formMatchesHTML .= 
                "<hr />
                <div class='form'>
                    $form
                </div>";
        }

        return $formMatchesHTML;
    }


    private function formMatchInfo($isPlayingAtHome, $match)
    {
        if ($isPlayingAtHome) {
            $opponentAbbreviatedName = $match->AwayAbbr;
            $opponentName = $match->AwayName;
            $opponentFullName = $match->AwayFull;

            $location = "(H)";

            $homeTeamWon = $match->HomeScore > $match->AwayScore;
            $awayTeamWon = $match->HomeScore < $match->AwayScore;

            if ($homeTeamWon) {
                $result = "win";
                $icon = "W";
            }
            else if ($awayTeamWon) {
                $result = "lose";
                $icon = "L";
            }
            else {
                $result = "draw";
                $icon = "D";
            }
        }
        else {
            $opponentAbbreviatedName = $match->HomeAbbr;
            $opponentName = $match->HomeName;
            $opponentFullName = $match->HomeFull;

            $location = "(A)";

            $homeTeamWon = $match->HomeScore > $match->AwayScore;
            $awayTeamWon = $match->HomeScore < $match->AwayScore;

            if ($homeTeamWon) {
                $result = "lose";
                $icon = "L";
            }
            else if ($awayTeamWon) {
                $result = "win";
                $icon = "W";
            }
            else {
                $result = "draw";
                $icon = "D";
            }
        }


        return (object) [
            "opponentAbbreviatedName" => $opponentAbbreviatedName,
            "opponentName" => $opponentName,
            "opponentFullName" => $opponentFullName,
            "homeScore" => $match->HomeScore,
            "awayScore" => $match->AwayScore,
            "location" => $location,
            "result" => $result,
            "icon" => $icon,
        ];
    }


    private function homeTeamFormHTML($match)
    {
        return 
            "<p>
                <span class='abbr' style='display: none;'> $match->opponentAbbreviatedName </span>
                <span class='norm' style='display: none;'> $match->opponentName </span>
                <span class='full' style='display: inline-block;'> $match->opponentFullName </span>
                $match->location 
            </p><p> $match->homeScore - $match->awayScore </p>
            <p class='result $match->result'> $match->icon </p>";
    }


    private function awayTeamFormHTML($match)
    {
        return 
            "<p class='result $match->result'> $match->icon </p>
            <p> $match->homeScore - $match->awayScore </p><p> 
                $match->location 
                <span class='abbr' style='display: none;'> $match->opponentAbbreviatedName </span>
                <span class='norm' style='display: none;'> $match->opponentName </span>
                <span class='full' style='display: inline-block;'> $match->opponentFullName </span>
            </p>";
    }


    function matchScoreOrKickOffTimeHTML($match)
    {
        if ($this->isScoresSet) {
            return 
                "<li> <p> " . $match->getHomeScore() . " </p> </li>
                <li> <p> - </p> </li>
                <li> <p> " . $match->getAwayScore() . " </p> </li>";
        }
        

        $currentDateTime = new DateTime();
        $hasWeekBegun = $this->week->getStart() <= $currentDateTime;

        if ($hasWeekBegun) {
            return 
                "<li> <p> F </p> </li>
                <li> <p> - </p> </li>
                <li> <p> F </p> </li>";
        }


        $homeTeamScoreSelector = $this->scoreSelect($match, true);
        $awayTeamScoreSelector = $this->scoreSelect($match, false);

        return 
            "<li> $homeTeamScoreSelector </li>
            <li> </li>
            <li> $awayTeamScoreSelector </li>";
    }


    private function scoreSelect($match, bool $isHomeTeam)
    {
        if ($isHomeTeam) {
            $scoreName = "homeScore".$match->getMatchNum();
            $score = $match->getHomeScore();
        }
        else {
            $scoreName = "awayScore".$match->getMatchNum();
            $score = $match->getAwayScore();
        }


        $scoreOptions = "";
        $maxScore = 10;
        for ($i = 0; $i <= $maxScore; $i++) {
            $isSelected = $score == $i;
            
            if ($isSelected)
                $scoreOptions .= "<option value='$i' selected> $i </option>";
            else
                $scoreOptions .= "<option value='$i'> $i </option>";
        }


        return 
            "<select class='score' name='$scoreName'> 
                <option value='-1'> </option>
                $scoreOptions 
            </select>";
    }


    private function jokerHTML($match)
    {
        $matchNum = $match->getMatchNum();
        $isJoker = $match->getJoker();

        if ($isJoker)
            $image = "<img name='ballImg' src='/images/Ball_Blue.png' checked />";
        else 
            $image = "<img name='ballImg' src='/images/Ball_White.png' />";


        if ($this->isScoresSet) {
            return 
                "<li class='joker'>
                    <input type='radio' name='joker' id='joker-$matchNum' value='$matchNum' />
                    <label for='joker-$matchNum'> $image </label>
                </li>";
        }
        else {
            return 
                "<li class='joker radio'>
                    <input type='radio' name='joker' id='joker-$matchNum' value='$matchNum' />
                    <label for='joker-$matchNum'> $image </label>
                </li>";
        }
    }


    private function saveOrEditButtonHTML() {
        $currentDateTime = new DateTime();
        $weekHasBegun = $this->week->getStart() <= $currentDateTime;

        if ($weekHasBegun) { return ""; }


        if ($this->isScoresSet) 
            $button = "<input type='submit' class='submitButton' name='edit' value='Edit Scores' />";
        else
            $button = "<input type='submit' class='submitButton' name='save' value='Set Scores' />";


        $weekNum = $this->week->getWeekNum();

        return 
            "<input type='hidden' name='week' value='$weekNum' />
            <div class='submit'>
                <hr />
                $button
            </div>";
    }


    private function scoresComplete($week) 
    {
        // Checks if all scoring has been done and whether to start on edit view

        $canScoresBeEdited = isset($_POST['edit']);
        if ($canScoresBeEdited) { return false; }


        foreach ($week->getMatches() as $match)
        {
            $isScoreSet = $match->getHomeScore() >= 0 && $match->getAwayScore() >= 0;
            if (!$isScoreSet) { return false; }
        }

        return true;
    }


    private function submitClicked($week) 
    {
        $scoresAreBeingSaved = isset($_POST['save']);
        if (!$scoresAreBeingSaved) { return; }


        $numOfMatches = count($week->getMatches());
        for ($i = 1; $i <= $numOfMatches; $i++) 
        {
            $match = $week->getMatches()[$i - 1];
            $homeScore = $_REQUEST['homeScore'.$i];
            $awayScore = $_REQUEST['awayScore'.$i];
            $isJoker = null;
            

            if (isset($_POST['joker'])) 
                $isJoker = $_POST['joker'] == $match->getMatchNum() ? true : false;
            

            (new GameService())->setScores($this->playerName, $this->season, $this->week->getWeekNum(), 
                $match, $homeScore, $awayScore, $isJoker);
        }

       
        echo "<meta http-equiv='refresh' content='0'>"; // Refresh page
    }
}
?>
