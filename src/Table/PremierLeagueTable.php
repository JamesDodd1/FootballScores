<?php
namespace Source\Table;

class PremierLeagueTable implements ILeagueTable
{
    protected $name = "Premier League", $positions, $numOfChampionsLeague, $numOfRelegated;

    public function __construct()
    {
        $this->positions = [];
        $this->numOfChampionsLeague = 4;
        $this->numOfRelegated = 3; 
    }

    public function getName(): string            { return $this->name; }
    public function getPositions(): array        { return $this->positions; }
    public function getChampionsLeaguePos(): int { return $this->numOfChampionsLeague; }
    public function getRelegationPos(): int      { return count($this->positions) - $this->numOfRelegated; }

    public function setPositions(array $positions) { $this->positions = $positions; }
}
?>
