<?php
namespace Tests\Unit\Source\Week;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Source\Club\Club;
use Source\Game\Game;
use Source\Week\Week;
use Source\Week\WeekService;

class WeekServiceTest extends TestCase
{
    /** @var WeekService */
    protected $weekService;
    private $sql_property, $sql_mock;

    public function setUp(): void
    {
        $this->weekService = new WeekService();

        $reflection = new \ReflectionClass($this->weekService);
        $this->sql_property = $reflection->getProperty('sql');
        $this->sql_property->setAccessible(true);

        $this->sql_mock = $this->getMockBuilder(\Database\Sql::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods([
                                   'getWeekGamesFromDatabase',
                                   'getClubFromDatabase',
                                   'getOngoingOrNextWeekNumFromDatabase',
                                   'getPreviousWeekNumFromDatabase',
                                   'getWeekFromDatabase',
                                   'getAllUsersWeekScoresFromDatabase',
                                   'getUsersWeekScoreFromDatabase',
                               ])
                               ->getMock();
    }

    public function tearDown(): void
    {
        unset($this->weekService);
        unset($this->sql_property);
        unset($this->sql_mock);
    }

    protected function sqlMock_setMethodReturnValue(string $methodName, $returnValue)
    {
        $this->sql_mock->expects($this->any())
                       ->method($methodName)
                       ->willReturn($returnValue);
        
        $this->sql_property->setValue($this->weekService, $this->sql_mock);
    }


    /** @test */
    public function weekGames_WeekExists_ReturnsArray()
    {
        $this->sqlMock_WeekGames_WeekExists();
        $this->clubServiceMock_WeekGames_WeekExists();
        $expected = $this->expected_WeekGames_WeekExists();

        $actual = $this->weekService->weekGames('', 0, 1, '');

        $this->assertEquals($expected, $actual);
    }
    private function sqlMock_WeekGames_WeekExists()
    {
        $this->sqlMock_setMethodReturnValue('getWeekGamesFromDatabase', [
            (object) [
                'HomeTeam' => '',
                'AwayTeam' => '',
                'HomeScore' => '0',
                'AwayScore' => '0',
                'KickOff' => '2020-01-01 00:00:00',
                'Win' => '0',
                'Draw' => '0',
                'Lose' => '0',
                'Joker' => '0',
            ]
        ]);
    }
    private function clubServiceMock_WeekGames_WeekExists()
    {
        $reflection = new \ReflectionClass($this->weekService);
        $clubService_property = $reflection->getProperty('clubService');
        $clubService_property->setAccessible(true);

        $clubService_mock = $this->getMockBuilder(\Source\Club\ClubService::class)
                                 ->disableOriginalConstructor()
                                 ->onlyMethods(['getClub'])
                                 ->getMock();

        $clubService_mock->expects($this->any())
                         ->method('getClub')
                         ->willReturn(new Club('', '', ''));
        
        $clubService_property->setValue($this->weekService, $clubService_mock);
    }
    private function expected_WeekGames_WeekExists()
    {
        return [
            new Game(
                1,
                new Club('', '', ''),
                new Club('', '', ''),
                0,
                0,
                new DateTime('2020-01-01'),
                false,
                false,
                false,
                false
            )
        ];
    }


    /** @test */
    public function weekGames_WeekDoesNotExist_ReturnsNull()
    {
        $this->sqlMock_setMethodReturnValue('getWeekGamesFromDatabase', []);

        $result = $this->weekService->weekGames('', 2020, 1, '');

        $this->assertNull($result);
    }


    /** @test */
    public function getCurrentWeekNum_LastestWeekIsOngoingOrUpcoming_ReturnsInteger()
    {
        $this->sqlMock_setMethodReturnValue('getOngoingOrNextWeekNumFromDatabase', [
            (object) ['WeekNum' => '1']
        ]);
        $expected = 1;

        $actual = $this->weekService->getCurrentWeekNum('', 0);

        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function getCurrentWeekNum_LastestWeekIsFinished_ReturnsInteger()
    {
        $this->sqlMock_setMethodReturnValue('getOngoingOrNextWeekNumFromDatabase', []);
        $this->sqlMock_setMethodReturnValue('getPreviousWeekNumFromDatabase', [
            (object) ['WeekNum' => '1']
        ]);
        $expected = 1;

        $actual = $this->weekService->getCurrentWeekNum('', 0);

        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function getCurrentWeekNum_NoWeeksFound_ReturnsNegativeInteger()
    {
        $this->sqlMock_setMethodReturnValue('getOngoingOrNextWeekNumFromDatabase', []);
        $this->sqlMock_setMethodReturnValue('getPreviousWeekNumFromDatabase', []);
        $expected = -1;

        $actual = $this->weekService->getCurrentWeekNum('', 0);

        $this->assertEquals($expected, $actual);
        
    }


    /** @test */
    public function getWeek_WeekExists_ReturnsWeekClass()
    {
        $this->sqlMock_setMethodReturnValue('getWeekFromDatabase', [
            (object) [
                'WeekNum' => '1',
                'StartTime' => '2020-01-01 00:00:00',
                'EndDate' => '2020-01-01 00:00:00'
            ]
        ]);
        $expected = new Week(1, new DateTime('2020-01-01'), new DateTime('2020-01-01'));
        
        $actual = $this->weekService->getWeek('', 0, 1);
        
        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function getWeek_WeekDoesNotExist_ReturnsNull()
    {
        $this->sqlMock_setMethodReturnValue('getWeekFromDatabase', []);

        $result = $this->weekService->getWeek('', 0, 1);

        $this->assertNull($result);
    }


    /** @test */
    public function getWeek_NoCurrentWeek_ReturnsNull()
    {
        $this->sqlMock_setMethodReturnValue('getOngoingOrNextWeekNumFromDatabase', []);
        $this->sqlMock_setMethodReturnValue('getPreviousWeekNumFromDatabase', []);
        
        $result = $this->weekService->getWeek('', 0, 0);

        $this->assertNull($result);
    }


    /** @test */
    public function getWeek_NegativeWeekNumber_ThrowsException()
    {
        $this->expectException(Exception::class);

        $this->weekService->getWeek('', 0, -1);
    }


    /** @test */
    public function getAllUsersWeekScores_WeekScoresExist_ReturnsArray()
    {
        $this->sqlMock_setMethodReturnValue('getAllUsersWeekScoresFromDatabase', [
            (object) ['WeekNum' => '1', '' => '0']
        ]);
        $expected = [ (object) [ 'WeekNum' => 1, '' => '0' ] ];

        $actual = $this->weekService->getAllUsersWeekScores('', 0);

        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function getAllUsersWeekScores_WeekScoresDoNotExist_ReturnsNull()
    {
        $this->sqlMock_setMethodReturnValue('getAllUsersWeekScoresFromDatabase', []);

        $result = $this->weekService->getAllUsersWeekScores('', 0);

        $this->assertNull($result);
    }


    /** @test */
    public function getUsersWeekScore_ScoreExists_ReturnsInteger()
    {
        $this->sqlMock_setMethodReturnValue('getUsersWeekScoreFromDatabase', [
            (object) ['Score' => '0']
        ]);
        $expected = 0;

        $actual = $this->weekService->getUsersWeekScore('', '', 0, 1);

        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function getUsersWeekScore_ScoreDoesNotExist_ReturnsInteger()
    {
        $this->sqlMock_setMethodReturnValue('getUsersWeekScoreFromDatabase', []);
        $expected = -1;

        $actual = $this->weekService->getUsersWeekScore('', '', 0, 1);

        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function getUsersWeekScore_NoCurrentWeek_ReturnsNegativeInteger()
    {
        $this->sqlMock_setMethodReturnValue('getOngoingOrNextWeekNumFromDatabase', []);
        $this->sqlMock_setMethodReturnValue('getPreviousWeekNumFromDatabase', []);
        $expected = -1;

        $actual = $this->weekService->getUsersWeekScore('', '', 0, 0);

        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function getUsersWeekScore_NegativeWeekNumber_ThrowsException()
    {
        $this->expectException(Exception::class);

        $this->weekService->getUsersWeekScore('', '', 0, -1);
    }
}
?>
