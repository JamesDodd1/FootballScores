<?php
namespace Tests\Unit\Database;

use PHPUnit\Framework\TestCase;
use Source\Club\Club;
use Source\Table\Position;
use Source\Table\TableService;

class TableServiceTest extends TestCase
{
    /** @var TableService */
    protected $tableService;
    private $sql_property, $sql_mock;

    public function setUp(): void
    {
        $this->tableService = new TableService();

        $reflection = new \ReflectionClass($this->tableService);
        $this->sql_property = $reflection->getProperty('sql');
        $this->sql_property->setAccessible(true);

        $this->sql_mock = $this->getMockBuilder(\Database\Sql::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods([
                                   'getLeaderboardFromDatabase',
                                   'getClubsStatsFromLeagueTableFromDatabase',
                               ])
                               ->getMock();
    }

    public function tearDown(): void
    {
        unset($this->tableService);
        unset($this->sql_property);
        unset($this->sql_mock);
    }

    protected function sqlMock_setMethodReturnValue(string $methodName, $returnValue)
    {
        $this->sql_mock->expects($this->any())
                       ->method($methodName)
                       ->willReturn($returnValue);
        
        $this->sql_property->setValue($this->tableService, $this->sql_mock);
    }


    /** @test */
    public function getLeaderboard_WeekScoresExist_ReturnArray()
    {
        $this->sqlMock_setMethodReturnValue('getLeaderboardFromDatabase', [
            (object) ['Name' => '', 'SeasonScore' => '0', 'WeekScore' => '0']
        ]);
        $expected = [ (object) ['Name' => '', 'SeasonScore' => '0', 'WeekScore' => '0'] ];

        $actual = $this->tableService->getLeaderboard('', 0);

        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function getLeaderboard_WeekScoresDoesNotExist_ReturnsArray()
    {
        $this->sqlMock_setMethodReturnValue('getLeaderboardFromDatabase', [
            (object) ['Name' => '', 'SeasonScore' => null, 'WeekScore' => null]
        ]);
        $expected = [ (object) ['Name' => '', 'SeasonScore' => null, 'WeekScore' => null] ];

        $actual = $this->tableService->getLeaderboard('', 0);

        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function getLeaderboard_NoUsers_ReturnsNull()
    {
        $this->sqlMock_setMethodReturnValue('getLeaderboardFromDatabase', []);

        $result = $this->tableService->getLeaderboard('', 0);

        $this->assertNull($result);
    }


    /** @test */
    public function getLeagueTable()
    {
        $this->sqlMock_getLeagueTable();
        $expected = [
            (object) ['Name' => '', 'Positions' => [
                new Position(new Club('', '', ''), 1, 0, 0, 1, 0),
                new Position(new Club('', '', ''), 0, 1, 0, 0, 0),
                new Position(new Club('', '', ''), 0, 1, 0, 0, 0),
                new Position(new Club('', '', ''), 0, 0, 1, 0, 1),
            ]]
        ];

        $actual = $this->tableService->getLeagueTable('', 0);

        $this->assertEquals($expected, $actual);
    }
    private function sqlMock_getLeagueTable()
    {
        $this->sqlMock_setMethodReturnValue('getClubsStatsFromLeagueTableFromDatabase', [
            (object) [
                'Name' => '', 'FullName' => '', 'Abbreviation' => '', 'TableName' => '',
                'Won' => '0', 'Drawn' => '1', 'Lost' => '0', 'GoalsFor' => '0', 'GoalsAgainst' => '0'
            ],
            (object) [
                'Name' => '', 'FullName' => '', 'Abbreviation' => '', 'TableName' => '',
                'Won' => '0', 'Drawn' => '1', 'Lost' => '0', 'GoalsFor' => '0', 'GoalsAgainst' => '0'
            ],
            (object) [
                'Name' => '', 'FullName' => '', 'Abbreviation' => '', 'TableName' => '',
                'Won' => '0', 'Drawn' => '0', 'Lost' => '1', 'GoalsFor' => '0', 'GoalsAgainst' => '1'
            ],
            (object) [
                'Name' => '', 'FullName' => '', 'Abbreviation' => '', 'TableName' => '',
                'Won' => '1', 'Drawn' => '0', 'Lost' => '0', 'GoalsFor' => '1', 'GoalsAgainst' => '0'
            ],
        ]);
    }
}

?>
