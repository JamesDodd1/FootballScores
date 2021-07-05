<?php
namespace Source\User;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/general.php';

use Database\Sql;
use PDO;

class UserService
{
    protected $sql;

    public function __construct() 
    {
        $this->sql = new Sql($this->databasePDO());
    }

    private function databasePDO()
    {
        $config = include DATABASE_CONFIG;

        $db = $config->connections->localhost;
        
        return new PDO(
            "mysql:host=$db->host; dbname=$db->database; charset=utf8",
            $db->username,
            $db->password
        );
    }


    /** 
     * Gets all users
     * 
     * @return array|null Array contain User objects
     */
    public function getAllUsers(): ?array // TESTED
    {
        $users = $this->sql->getAllUsersFromDatabase();

        if (empty($users)) { return null; }
        
        $allUsers = [];
        foreach ($users as $user) {
            $allUsers[] = new User($user->Name, $user->Answers);
        } 

        return $allUsers;
    }


    /** 
     * Get a user
     * 
     * @param string $user Name of the user
     * 
     * @return User|null Object of User
     */
    public function getUser(string $user): ?User // TESTED
    {
        $user = $this->sql->getUserFromDatabase($user);

        if (count($user) == 0) { return null; }

        return new User($user[0]->Name, $user[0]->Answers);
    }
}
?>
