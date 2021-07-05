<?php
namespace Tests\Unit\Database;

require_once 'vendor/autoload.php';
require_once 'config/general.php';

use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Source\Club\ClubService;

class ClubServiceTest extends TestCase
{
    /** @var ClubService */
    protected $clubService;
    private $sql_property, $sql_mock;

    public function setUp(): void
    {
        $this->clubService = new ClubService();

        $reflection = new ReflectionClass($this->clubService);
        $this->sql_property = $reflection->getProperty('sql');
        $this->sql_property->setAccessible(true);

        $this->sql_mock = $this->getMockBuilder(\Database\Sql::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods([
                                   'getClubFormFromDatabase',
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
        
        $this->sql_property->setValue($this->clubService, $this->sql_mock);
    }


    /** @test */
    public function getClub()
    {

    }


    /** @test */
    public function getClubsForm_ClubFormExists_ReturnsArray()
    {
        $this->sqlMock_setMethodReturnValue('getClubFormFromDatabase', [
            (object) [
                'HomeAbbr' => '', 'HomeName' => '', 'HomeFull' => '', 'HomeScore' => '0',
                'AwayAbbr' => '', 'AwayName' => '', 'AwayFull' => '', 'AwayScore' => '0',
            ]
        ]);
        $expected = [
            (object) [
                'HomeAbbr' => '', 'HomeName' => '', 'HomeFull' => '', 'HomeScore' => '0',
                'AwayAbbr' => '', 'AwayName' => '', 'AwayFull' => '', 'AwayScore' => '0',
            ]
        ];
        
        $actual = $this->clubService->getClubsForm('', new DateTime(), 1);

        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function getClubsForm_ClubFormDoesNotExist_ReturnsNull()
    {
        $this->sqlMock_setMethodReturnValue('getClubFormFromDatabase', []);
        
        $result = $this->clubService->getClubsForm('', new DateTime());

        $this->assertNull($result);
    }
}
?>
