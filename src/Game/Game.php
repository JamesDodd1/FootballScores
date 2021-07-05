<?php
namespace Source\Game;

use DateTime;
use Source\Club\Club;

class Game //extends Week
{
    private $matchNum, $homeTeam, $awayTeam, $homeScore, $awayScore, $kickOff, $win, $draw, $lose, $joker;

    /**  */
    public function __construct(int $matchNum, Club $homeTeam, Club $awayTeam, int $homeScore, int $awayScore,
        DateTime $kickOff, bool $win, bool $draw, bool $lose, bool $joker)
    {
        $this->matchNum = $matchNum > 0 ? $matchNum : 1;

        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;

        $this->homeScore = $homeScore >= -1 ? $homeScore : -1;
        $this->awayScore = $awayScore >= -1 ? $awayScore : -1;

        $this->kickOff = $kickOff;

        $this->win = $win;
        $this->draw = $draw;
        $this->lose = $lose;

        $this->joker = $joker;
    }

    public function getMatchNum(): int     { return $this->matchNum; }
    public function getHomeTeam(): Club    { return $this->homeTeam; }
    public function getAwayTeam(): Club    { return $this->awayTeam; }
    public function getHomeScore(): int    { return $this->homeScore; }
    public function getAwayScore(): int    { return $this->awayScore; }
    public function getKickOff(): DateTime { return $this->kickOff; }
    public function getWin(): bool         { return $this->win; }
    public function getDraw(): bool        { return $this->draw; }
    public function getLose(): bool        { return $this->lose; }
    public function getJoker(): bool       { return $this->joker; }

    //public function WeekStart() { return parent::getStart(); }
    //public function WeekEnd()   { return parent::getEnd(); }
}
?>
