<?php
namespace Source\Season;

use DateTime;

class Season 
{
    private $name, $start, $end, $week;

    /**
     * @param DateTime $start Beginning date of the season
     * @param DateTime $end   End date of the season
     */
    public function __construct(DateTime $start, DateTime $end) 
    { 
        $this->start = $start;
        $this->end = $end;

        $this->name = "";
        $this->week = [];
    }

    public function getName(): string    { return $this->name; }
    public function getStart(): DateTime { return $this->start; }
    public function getEnd(): DateTime   { return $this->end; }
    /** 
     * @return array[Week] 
     * */
    public function getWeek(): array     { return $this->week; }

    /** 
     * @param array[Week] $week
     */
    public function setWeek(array $week) { $this->week = $week; }

    //protected function getSeason() { return new self($this->start, $this->end); }
}
?>
