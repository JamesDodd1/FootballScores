<?php
namespace Tests\Unit\Database;

use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Source\Season\Season;
use Source\Season\SeasonService;

class SeasonServiceTest extends TestCase
{
    /** @var SeasonService */
    protected $seasonService;
    private $sql_property, $sql_mock;

    public function setUp(): void
    {
        $this->seasonService = new SeasonService();

        $reflection = new ReflectionClass($this->seasonService);
        $this->sql_property = $reflection->getProperty('sql');
        $this->sql_property->setAccessible(true);

        $this->sql_mock = $this->getMockBuilder(\Database\Sql::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods([
                                   'getSeasonFromDatabase',
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
        
        $this->sql_property->setValue($this->seasonService, $this->sql_mock);
    }


    /** @test */
    public function getSeason_SeasonExists_ReturnsSeasonClass()
    {
        $this->sqlMock_setMethodReturnValue('getSeasonFromDatabase', [
            (object) ['StartDate' => '2020-01-01', 'EndDate' => '2020-01-01']
        ]);
        $expected = new Season(new DateTime('2020-01-01'), new DateTime('2020-01-01'));

        $actual = $this->seasonService->getSeason('', 1);

        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function getSeason_SeasonDoesNotExist_ReturnsNull()
    {
        $this->sqlMock_setMethodReturnValue('getSeasonFromDatabase', []);

        $result = $this->seasonService->getSeason('', 1);

        $this->assertNull($result);
    }


    /** @test */
    public function getTotalNumberOfWeeks()
    {

    }


    /** @test */
    public function getUsersSeasonScore()
    {

    }
}
?>
