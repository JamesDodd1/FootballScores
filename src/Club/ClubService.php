<?php
namespace Source\Club;

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/general.php';

use Database\Sql;
use DateTime;
use PDO;

class ClubService
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


    /**  */
    private function getAllClubs(string $competition, int $year): array // UNUSED
    {
        $clubs = $this->sql->getAllClubsFromDatabase($competition, $year);

        $allClubs = [];
        foreach ($clubs as $club)
        {
            $allClubs[] = new Club($club->Name, $club->FullName, $club->Abbreviation);
        }
        
        return $allClubs;
    }


    /**  */
    public function getClub(string $fullName): ?Club
    {
        $club = $this->sql->getClubFromDatabase($fullName);

        if (empty($club)) { return null; }

        return new Club($club[0]->Name, $club[0]->FullName, $club[0]->Abbreviation);
    }



    public function getClubsForm(string $fullName, DateTime $from, int $numOfGames = 5) 
    {
        $clubForm = $this->sql->getClubFormFromDatabase($fullName, $from, $numOfGames);

        if (empty($clubForm)) { return null; }
        
        return $clubForm;
    }
}
?>
