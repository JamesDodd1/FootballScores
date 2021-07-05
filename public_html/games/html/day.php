<?php
namespace FootballScores\Games\HTML;

use DateTime;

class Day
{
    protected $openingDiv, $closingDiv, $date;

    public function __construct(DateTime $date)
    {
        $this->openingDiv = "<div class='day'>";
        $this->closingDiv = "</div>";
        $this->date = $date;
    }


    public function getOpeningDiv() { return $this->openingDiv; }
    public function getClosingDiv() { return $this->closingDiv; }
    public function getDay()
    {
        $day = $this->date->format("l jS F");

        return "<h3> $day </h3>";
    }
}

?>