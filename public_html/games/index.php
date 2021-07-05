
<?php 
    $root = $_SERVER['DOCUMENT_ROOT'] . "/public_html";
	//header('Location: https://google.co.uk');
	//echo $_SERVER['HTTP_HOST'];
	include_once "$root/database/database.php";
	$configs = include "$root/database/config.php";

	$database = new FootballScores\Database\Database();
	$database->connect($configs->host, $configs->username, $configs->password, $configs->database);
	
	include_once "$root/database/sql.php";
	$db = new Sql($database->getConnection());


    // Set Variables
	$user = getUser();
	$competition = getCompetition();
	$season = getSeason($competition);
	$weekNum = getWeekNum($competition, $season);
	echo "hello";

	if (is_null($user) || is_null($competition) || is_null($competition) || is_null($weekNum)) {
		
		if (is_null($user)) { $user = $db->getUser("Results"); }
		if (is_null($competition)) { $competition = "euros"; }
		if (is_null($season)) { $season = $db->getCurrentSeason($competition); }
		if (is_null($weekNum)) { $weekNum = $db->getCurrentWeek($competition, $season)->getWeekNum(); }

		header("Location: http://" . $_SERVER['HTTP_HOST'] . "/games/" . $user->getName() . "/$season/$weekNum");
	}
?>

<!DOCTYPE html>
<HTML>

<head>
	<title> Football Scores - <?php echo $user->getName(); ?> </title>
	
	<meta charset="utf-8" />
	
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />	
	
	<?php include_once "$root/icons.php"; ?>
	
	<link href="/style/page_v1.1.0.css" type="text/css" rel="stylesheet" />
	<link href="/style/home_v1.1.0.css" type="text/css" rel="stylesheet" />
	<link href="/style/games_v1.1.0.css" type="text/css" rel="stylesheet" />
</head>


<body>
	<?php include_once "$root/nav_bar.php"; ?>
	
	
	<div id="page">
		<h1 class="name"> 
			<?php echo $user->getName(); ?> 
		</h1>


		<div id="content" class="main"> 
			<?php include_once "./weekNumSelector.php"; ?>

			<hr />

            <?php
                include_once "./weekGames.php";

				if ($user->getIsAnswers()) {
					$resultWeekGames = new ResultWeekGames($database->getConnection());
                	echo $resultWeekGames->gameWeekMatchesHTML($season, $weekNum);
				}
				else {
					$playerWeekGames = new PlayerWeekGames($database->getConnection(), $user->getName());
					echo $playerWeekGames->gameWeekMatchesHTML($season, $weekNum);
				}
            ?>
        </div>

		<div id="content" class="groupScores">
			<?php
			include_once "$root/scores/leaderboardFactory.php";

			$leaderboard = LeaderboardFactory::create("euros", 2021);
			echo $leaderboard->generateHTML();
			?>
		</div>

		<div id="content" class="league">
			<?php
			include_once "$root/table/tableFactory.php";

			$table = TableFactory::create("euros", 2021);
			echo $table->generateHTML();
			?>
		</div>
    </div>
</body>

</HTML>

<?php
	function getUser(): ?User
	{
		global $db;

		if (!isset($_GET['user'])) { return null; }

		return $db->getUser($_GET['user']);
	}


	function getCompetition()
	{
		global $db;

		//if (!isset($_GET['comeptition'])) { return null; }

		return "euros";
	}


	function getSeason($competition)
	{
		global $db;

		if (!isset($_GET['season'])) { return null; }
		echo ($competition);
		return $db->getSeason($competition, isset($_GET['season']));
	}


	function getWeekNum($competition, $year)
	{
		global $db;

		if (!isset($_GET['weekNum'])) { return null; }

		return $db->getCurrentWeek($competition, $year, isset($_GET['weekNum']))->WeekNum;
	}
?>
