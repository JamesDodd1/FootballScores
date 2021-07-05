<?php
namespace Tests\Unit\Database;

use Database\Sql;
use PHPUnit\Framework\TestCase;

class SqlTest extends TestCase
{
    /** @var \PDO */
    protected static $pdo;

    public static function setUpBeforeClass(): void
    {
        $config = require 'config/database.php';
        $db = $config->connections->test;
        self::$pdo = new \PDO(
            "mysql:host=$db->host; dbname=$db->database; charset=utf8",
            $db->username,
            $db->password
        );
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdo == null;
    }

    public function setUp(): void
    {
        self::$pdo->beginTransaction();
    }

    public function tearDown(): void
    {
        self::$pdo->rollBack();
    }


    /** @test */
    public function getAllUsersFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getUserFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getWeekGamesFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getOngoingOrNextWeekNumFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getPreviousWeekNumFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getWeekFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getUsersWeekScoreFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function setScoresInDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getAllUsersWeekScoresFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getUsersSeasonScoreFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getLeaderboardFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getClubsStatsFromLeagueTableFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getAllClubsFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getTotalNumOfGamesForCompetitionFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getClubFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getSeasonFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }


    /** @test */
    public function getClubFormFromDatabase()
    {
        $sql = new Sql(self::$pdo);
    }
}

?>
