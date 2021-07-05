<?php
namespace Source\Week;

use DateTime;

class Week
{
    private $weekNum, $startTime, $endDate, $matches;

    public function __construct(int $weekNum, DateTime $start, DateTime $end) 
    { 
        $this->weekNum = $weekNum; 
        $this->startTime = $start;
        $this->endDate = $end;

        $this->matches = [];
    }

    public function getWeekNum(): int    { return $this->weekNum; }
    public function getStart(): DateTime { return $this->startTime; }
    public function getEnd(): DateTime   { return $this->endDate; }
    public function getMatches(): array  { return $this->matches; }

    public function setMatches(array $matches) { $this->matches = $matches; }

    //public function getSeasonStart(): DateTime { return parent::getStart(); }
    //public function getSeasonEnd(): DateTime   { return parent::getEnd(); }

    //protected function getSeason() { return parent::getSeason(); }
}
?>
