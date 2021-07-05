<?php
namespace Tests\Unit\Source;

require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Source\User\User;
use Source\User\UserService;

class UserServiceTest extends TestCase
{
    /** @var UserService */
    protected $userService;
    private $sql_property, $sql_mock;

    public function setUp(): void
    {
        $this->userService = new UserService();

        $reflection = new \ReflectionClass($this->userService);
        $this->sql_property = $reflection->getProperty('sql');
        $this->sql_property->setAccessible(true);

        $this->sql_mock = $this->getMockBuilder(\Database\Sql::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods([
                                   'getAllUsersFromDatabase',
                                   'getUserFromDatabase',
                               ])
                               ->getMock();
    }

    public function tearDown(): void
    {
        unset($this->userService);
        unset($this->sql_property);
        unset($this->sql_mock);
    }

    protected function sqlMock_setMethodReturnValue(string $methodName, $returnValue)
    {
        $this->sql_mock->expects($this->any())
                       ->method($methodName)
                       ->willReturn($returnValue);
        
        $this->sql_property->setValue($this->userService, $this->sql_mock);
    }


    /** @test */
    public function getAllUsers_AllUsersRetrievedFromDatabase_ReturnsArrayOfUserClass()
    {
        $this->sqlMock_setMethodReturnValue('getAllUsersFromDatabase', [
            (object) ['Name' => '', 'Answers' => '0'],
            (object) ['Name' => '', 'Answers' => '0']
        ]);
        $expected = [ new User ('', false ), new User ('', false ) ];

        $actual = $this->userService->getAllUsers();

        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function getAllUsers_NoUsersFoundInDatabase_ReturnsNull()
    {
        $this->sqlMock_setMethodReturnValue('getAllUsersFromDatabase', []);

        $result = $this->userService->getAllUsers();

        $this->assertNull($result);
    }


    /** @test */
    public function getUser_UserRetrievedFromDatabase_ReturnsUserClass()
    {
        $this->sqlMock_setMethodReturnValue('getUserFromDatabase', [
            (object) ['Name' => '', 'Answers' => '0']
        ]);
        $expected = new User ('', false );

        $actual = $this->userService->getUser('');

        $this->assertEquals($expected, $actual);
    }
    

    /** @test */
    public function getUser_NoUserFoundInDatabase_ReturnsNull()
    {
        $this->sqlMock_setMethodReturnValue('getUserFromDatabase', []);
        
        $result = $this->userService->getUser('');

        $this->assertNull($result);
    }
}
?>
