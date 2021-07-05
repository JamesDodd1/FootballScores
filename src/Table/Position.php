<?php
namespace Source\Table;

use Source\Club\Club;

class Position 
{
    protected $club, $won, $drawn, $lost, $goalsFor, $goalsAgainst;

    /**
     * @param Club $club
     * @param int $won
     * @param int $drawn
     * @param int $lost
     * @param int $goalsFor
     * @param int $goalsAgainst
     */
    public function __construct(Club $club, int $won, int $drawn, int $lost, int $goalsFor, int $goalsAgainst)
    {
        $this->club = $club;
        $this->won = $won;
        $this->drawn = $drawn;
        $this->lost = $lost;
        $this->goalsFor = $goalsFor;
        $this->goalsAgainst = $goalsAgainst;
    }

    public function getPlayed(): int         { return $this->won + $this->drawn + $this->lost; }
    public function getClub(): Club          { return $this->club; }
    public function getWon(): int            { return $this->won; }
    public function getDrawn(): int          { return $this->drawn; }
    public function getLost(): int           { return $this->lost; }
    public function getGoalsFor(): int       { return $this->goalsFor; }
    public function getGoalsAgainst(): int   { return $this->goalsAgainst; }
    public function getGoalDifference(): int { return $this->goalsFor - $this->goalsAgainst; }
    public function getPoints(): int         { return $this->won * 3 + $this->drawn; } 
}
?>