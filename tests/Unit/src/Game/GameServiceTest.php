<?php
namespace Tests\Unit\Database;

require_once 'vendor/autoload.php';

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Source\Club\Club;
use Source\Game\Game;
use Source\Game\GameService;

class GameServiceTest extends TestCase
{
    /** @var GameService */
    protected $gameService;
    private $sql_property, $sql_mock;

    public function setUp(): void
    {
        $this->gameService = new GameService();

        $reflection = new \ReflectionClass($this->gameService);
        $this->sql_property = $reflection->getProperty('sql');
        $this->sql_property->setAccessible(true);

        $this->sql_mock = $this->getMockBuilder(\Database\Sql::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods([
                                   'setScoresInDatabase',
                               ])
                               ->getMock();
    }

    public function tearDown(): void
    {
        unset($this->seasonService);
        unset($this->sql_property);
        unset($this->sql_mock);
    }

    protected function sqlMock_setMethodReturnValue(string $methodName, $returnValue)
    {
        $this->sql_mock->expects($this->any())
                       ->method($methodName)
                       ->willReturn($returnValue);
        
        $this->sql_property->setValue($this->gameService, $this->sql_mock);
    }


    /** @test */
    public function setScores()
    {
        $this->sqlMock_setMethodReturnValue('setScoresInDatabase', true);

        $result = $this->gameService->setScores('', 0, 1,
            new Game(
                1, new Club('', '', ''), new Club('', '', ''),
                0, 0, new DateTime(), false, false, false, false
            ),
        0, 0, false);

        $this->assertTrue($result);
    }


    /** @test */
    public function setScores_ZeroOrNegativeWeekNumber_ThrowsException()
    {
        $this->expectException(Exception::class);
        
        $this->gameService->setScores('', 0, -1,
            new Game(
                1, new Club('', '', ''), new Club('', '', ''),
                0, 0, new DateTime('2020-01-01'), false, false, false, false
            ),
        0, 0, false);
    }
}
?>
