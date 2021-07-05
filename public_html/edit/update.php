<?php

$root = $_SERVER['DOCUMENT_ROOT'] . "/public_html";

require_once "$root/database/crud.php";
use FootballScores\Database\Crud;

include_once "$root/database/database.php";
$configs = include "$root/database/config.php";

$database = new FootballScores\Database\Database();
$database->connect($configs->host, $configs->username, $configs->password, $configs->database);
?>

<HTML>
<head>
</head>

<body>

<?php
	$update = new Update($database->getConnection());
	
	echo $update->generateHTML("Premier League", 2020);
?>

</body>
</HTML>

<?php
class Update
{
	private $con;
	private $season = 3;

	private $crud;

	private $competition, $year;


    public function __construct(PDO $databaseConnection)
    {
        $this->crud = new Crud($databaseConnection);
    }
	
	
	public function generateHTML(string $competition, int $year)
	{
		$this->competition = $competition;
		$this->year = $year;

		$this->updateGames();

		return 
			$this->weekSelector() .
			$this->weekGames();
	}


	private function weekSelector()
	{
		return 
			"<form method='GET'>
				<select onchange='this.form.submit()' class='weekCombo' name='week'>
					<option value='0'> Select </option>" .
					$this->weekSelectorOptions() .
				"</select>
			</form>";
	}


	private function weekSelectorOptions()
	{
		$options = "";

		for ($i = 1; $i < 39; $i++) {
			$options .= "<option value='$i'> Week $i </option>";
		}

		return $options;
	}


	private function weekGames()
	{
		if (!isset($_GET['week'])) { return; }

		$weekNum = $_GET['week'];

		return
			"<h3> Week $weekNum </h3>" .
			$this->gamesToUpdate($weekNum);
	}


	private function gamesToUpdate(int $weekNum)
	{
		return 
			"<form method='POST'>
				<input type='hidden' name='week' value='$weekNum'>
				<br />".
				$this->allGamesToUpdate($weekNum) .
				"<input type='submit' name='change' value='Update Games'>
			</form>";
	}


	private function allGamesToUpdate(int $weekNum)
	{
		$gamesToUpdate = $this->getAllGamesToUpdateFromDatabase($weekNum);

		$allGames = "";

		for ($i = 1; $i <= count($gamesToUpdate); $i++) {
			$game = $gamesToUpdate[$i - 1];

			$allGames .= 
				"<pre> Home Team:  <input type='text' name='homeTeam-$i' size='30' value='$game->HomeTeam' readonly> </pre>
				<pre> Home Score: <input type='text' name='homeScore-$i' size='30' value='$game->HomeScore'> </pre>
				<pre> Away Team:  <input type='text' name='awayTeam-$i' size='30' value='$game->AwayTeam' readonly> </pre>
				<pre> Away Score: <input type='text' name='awayScore-$i' size='30' value='$game->AwayScore'> </pre>
				<br>";
		}

		return $allGames;
	}


	private function getAllGamesToUpdateFromDatabase(int $weekNum)
	{
		$sql = "SELECT ch.`FullName` AS HomeTeam, sc.`HomeScore`, ca.`FullName` AS AwayTeam, sc.`AwayScore`
				FROM `Competition` c
				INNER JOIN `Season` s
					ON c.`CompetitionID` = s.`CompetitionID`
				INNER JOIN `Week` w
					ON s.`SeasonID` = w.`SeasonID`
				INNER JOIN `Game` g
					ON w.`WeekID` = g.`WeekID`
				INNER JOIN `Score` sc
					ON g.`GameID` = sc.`GameID`
				INNER JOIN Club ch
					ON g.`HomeTeam` = ch.`ClubID`
				INNER JOIN Club ca
					ON g.`AwayTeam` = ca.`ClubID`
				INNER JOIN `User` u
					ON sc.`UserID` = u.`UserID`
				WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
					AND w.`WeekNum` = ? AND u.`Answers` = 1
				ORDER BY g.`KickOff` ASC, ch.`FullName` ASC, ca.`FullName` ASC";

		return $this->crud->select($sql, [$this->competition, $this->year, $weekNum]);
	}


	private function updateGames()
	{
		if (!isset($_POST['change'])) { return; }

		$currentSavedResults = $this->getCurrentSavedResultsFromDatabase();
		$usersScores = $this->getUsersGameScoresFromDatabase();

		for ($i = 1; $i <= count($currentSavedResults); $i++) {
			$result = $currentSavedResults[$i];

			// no scores set
			if ($result->HomeScore < 0 || $result->AwayScore < 0) {
				$this->updateResultsInDatabase($i);
				$this->updateUsersScoreResults($usersScores);
			}
		}
	}


	private function getCurrentSavedResultsFromDatabase()
	{
		$weekNum = $_GET['week'];

		$sql = "SELECT `HomeScore`, `AwayScore`
				FROM `Competition` c
				INNER JOIN `Season` s
					ON c.`CompetitionID` = s.`CompetitionID`
				INNER JOIN `Week` w
					ON s.`SeasonID` = w.`SeasonID`
				INNER JOIN `Game` g
					ON w.`WeekID` = g.`WeekID`
				INNER JOIN `Club` ch
					ON g.`HomeTeam` = ch.`ClubID`
				INNER JOIN `Club` ca
					ON g.`AwayTeam` = ca.`ClubID`
				INNER JOIN `Score` sc
					ON g.`GameID` = sc.`GameID`
				INNER JOIN `User` u
					ON sc.`UserID` = u.`UserID`
				WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
					AND w.`WeekNum` = ? AND u.`Answers` = 1
				ORDER BY g.`KickOff` ASC, ch.`FullName` ASC, ca.`FullName` ASC";

		return $this->crud->select($sql, [$this->competition, $this->year, $weekNum]);
	}


	private function getUsersGameScoresFromDatabase()
	{
		$users = $this->getAllUsersFromDatabase();
		$weekNum = $_GET['week'];

		$allUsers = [];

		foreach ($users as $user) {
			$sql = "SELECT `HomeScore`, `AwayScore`, sc.`Joker`
					FROM `Competition` c
					INNER JOIN `Season` s
						ON c.`CompetitionID` = s.`CompetitionID`
					INNER JOIN `Week` w
						ON s.`SeasonID` = w.`SeasonID`
					INNER JOIN `Game` g
						ON w.`WeekID` = g.`WeekID`
					INNER JOIN `Club` ch
						ON g.`HomeTeam` = ch.`ClubID`
					INNER JOIN `Club` ca
						ON g.`AwayTeam` = ca.`ClubID`
					INNER JOIN `Score` sc
						ON g.`GameID` = sc.`GameID`
					INNER JOIN `User` u
						ON sc.`UserID` = u.`UserID`
					WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
						AND w.`WeekNum` = ? AND u.`Name` = ?
					ORDER BY u.`UserID` ASC, g.`KickOff` ASC, ch.`FullName` ASC, ca.`FullName` ASC";
			
			$allUsers[] = (object) [
				'Name' => $user->Name,
				'Scores' => $this->crud->select($sql, [$this->competition, $this->year, $weekNum, $user->Name]),
			];
		}

		return $allUsers;
	}


	private function getAllUsersFromDatabase()
	{
		$sql = "SELECT `Name`
				FROM `User`
				WHERE `Answers` = 0
				ORDER BY `Name` ASC";

		return $this->crud->select($sql);
	}


	private function updateResultsInDatabase(int $gameNum)
	{
		$weekNum = $_GET['week'];

		$homeTeam = $_POST["homeTeam-$gameNum"];
		$awayTeam = $_POST["awayTeam-$gameNum"];
		$homeScore = $_POST["homeScore-$gameNum"];
		$awayScore = $_POST["awayScore-$gameNum"];

		$sql = "UPDATE `Score`
				SET `HomeScore` = ?, `AwayScore` = ?
				WHERE `ScoreID` = (
					SELECT sc.`ScoreID`
					FROM `Competition` c
					INNER JOIN `Season` s
						ON c.`CompetitionID` = s.`CompetitionID`
					INNER JOIN `Week` w
						ON s.`SeasonID` = w.`SeasonID`
					INNER JOIN `Game` g
						ON w.`WeekID` = g.`WeekID`
					INNER JOIN `Club` ch
						ON g.`HomeTeam` = ch.`ClubID`
					INNER JOIN `Club` ca
						ON g.`AwayTeam` = ca.`ClubID`
					INNER JOIN `Score` sc
						ON g.`GameID` = sc.`GameID`
					INNER JOIN `User` u
						ON g.`UserID` = u.`UserID`
					WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
						AND w.`WeekNum` = ? AND u.`Answers` = 1
						AND ch.`FullName` = ? AND ca.`FullName` = ?
				)";
		
		$this->crud->insert($sql, [
			$homeScore, $awayScore, $this->competition, $this->year, $weekNum, $homeTeam, $awayTeam
		]);
	}


	private function updateUsersScoreResults(array $users)
	{
		$weekNum = $_GET['week'];
		$weekScore = 0;

		foreach ($users as $user) {
			for ($i = 1; $i <= count($user->Scores); $i++) {
				$game = $user->Scores[$i];
				$homeScore = $_POST["homeScore-$i"];
				$awayScore = $_POST["awayScore-$i"];

				if ($homeScore < 0 || $awayScore < 0) { continue; }

				$scoreStatus = $this->getUserScoreResultStatus($game->HomeScore, $game->AwayScore, $homeScore, $awayScore);
				$joker = $game->Joker ? 2 : 1;

				switch ($scoreStatus) {
					case "won":
						$weekScore += 3 * $joker;
						break;
					case "drawn":
						$weekScore += 1 * $joker;
						break;
					default:
						break;
				}

				$this->updateUserScoreFlagsInDatabase($i, $scoreStatus);
			}

			$this->updateWeekScoreInDatabase($user->Name, $weekNum, $weekScore);
			$this->updateSeasonScoreInDatabase($user->Name, $weekScore);
		}
	}


	private function getUserScoreResultStatus(
		int $userHomeScore, int $userAwayScore, int $actualHomeScore, int $actualAwayScore
	)
	{
		if ($userHomeScore == $actualHomeScore && $userAwayScore == $actualAwayScore) { return "won"; }

		if (($userHomeScore > $userAwayScore && $actualHomeScore > $actualAwayScore) ||
			($userHomeScore < $userAwayScore && $actualHomeScore < $actualAwayScore) ||
			($userHomeScore == $userAwayScore && $actualHomeScore == $actualAwayScore)) {
			return "drawn";
		}

		return "lost";
	}


	private function updateUserScoreFlagsInDatabase(int $gameNum, string $status)
	{
		$won = false;
		$drawn = false;
		$lost = false;

		switch ($status) {
			case "won":
				$won = true;
				break;
			case "drawn":
				$drawn = true;
				break;
			default:
				$lost = true;
				break;
		}


		$weekNum = $_GET['week'];
		$homeTeam = $_POST["homeTeam-$gameNum"];
		$awayTeam = $_POST["awayTeam-$gameNum"];
		$homeScore = $_POST["homeScore-$gameNum"];
		$awayScore = $_POST["awayScore-$gameNum"];

		$sql = "UPDATE `Score`
				SET `Win` = ?, `Draw` = ?, `Lose` = ?
				WHERE ScoreID = (
					SELECT sc.`ScoreID`
					FROM `Competition` c
					INNER JOIN `Season` s
						ON c.`CompetitionID` = s.`CompetitionID`
					INNER JOIN `Week` w
						ON s.`SeasonID` = w.`SeasonID`
					INNER JOIN `Game` g
						ON w.`WeekID` = g.`WeekID`
					INNER JOIN `Club` ch
						ON g.`HomeTeam` = ch.`ClubID`
					INNER JOIN `Club` ca
						ON g.`AwayTeam` = ca.`ClubID`
					INNER JOIN `Score` sc
						ON g.`GameID` = sc.`GameID`
					INNER JOIN `User` u
						ON g.`UserID` = u.`UserID`
					WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
						AND w.`WeekNum` = ? AND u.`Name` = ?
						AND ch.`FullName` = ? AND ca.`FullName` = ?
				)";

		$this->crud->insert($sql, [
			$won,
			$drawn,
			$lost,
			$homeScore,
			$awayScore,
			$this->competition,
			$this->year,
			$weekNum,
			$homeTeam,
			$awayTeam
		]);
	}


	private function updateWeekScoreInDatabase(string $user, int $weekNum, int $score)
	{
		$sql = "UPDATE `WeekScore`
				SET `Score` = ?
				WHERE `WeekScoreID` = (
					SELECT ws.`WeekScoreID`
					FROM `WeekScore` ws
					INNER JOIN `User` u
						ON ws.`UserID` = u.`UserID`
					INNER JOIN `Week` w
						ON ws.`WeekID` = w.`WeekID`
					INNER JOIN `Season` s
						ON w.`SeasonID` = s.`SeasonID`
					INNER JOIN `Competition` c
						ON s.`CompetitionID` = c.`CompetitionID`
					WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
						AND w.`WeekNum` = ? AND u.`Name` = ?
				)";
		
		$this->crud->insert($sql, [$score, $this->competition, $this->year, $weekNum, $user]);
	}


	private function updateSeasonScoreInDatabase(string $user, int $increaseScoreBy)
	{
		$sql = "UPDATE `SeasonScore`
				SET `Score` = `Score` + ?
				WHERE `SeasonScoreID` = (
					SELECT ss.`SeasonScoreID`
					FROM `SeasonScore` ss
					INNER JOIN `User` u
						ON ss.`UserID` = u.`UserID`
					INNER JOIN `Season` s
						ON ss.`SeasonID` = s.`SeasonID`
					INNER JOIN `Competition` c
						ON s.`CompetitionID` = c.`CompetitionID`
					WHERE c.`Name` = ? AND YEAR(s.`StartDate`) = ?
						AND u.`Name` = ?
				)";
		
		$this->crud->insert($sql, [$increaseScoreBy, $this->competition, $this->year, $user]);
	}
}
?>
