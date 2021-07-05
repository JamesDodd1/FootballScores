<?php
namespace Tests\Unit\Games\HTML;

require_once 'vendor/autoload.php';

use DateTime;
use FootballScores\Games\HTML\Day;
use PHPUnit\Framework\TestCase;

class DayTest extends TestCase
{
    /** @test */
    public function testGetOpeningDiv_OpeningDivRetrieved_ReturnString()
    {
        $day = new Day(new DateTime('2020-01-01'));
        $expect = "<div class='day'>";

        $result = $day->getOpeningDiv();

        $this->assertEquals($expect, $result);
    }


    /** @test */
    public function testGetClosingDiv_ClosingDivRetrieved_ReturnString()
    {
        $day = new Day(new DateTime('2020-01-01'));
        $expect = "</div>";

        $result = $day->getClosingDiv();

        $this->assertEquals($expect, $result);
    }


    /** @test */
    public function testGetDay_DayRetrieved_ReturnString()
    {
        $day = new Day(new DateTime('2020-01-01'));
        $expect = "<h3> Wednesday 1st January </h3>";

        $result = $day->getDay();

        $this->assertEquals($expect, $result);
    }
}

?>
