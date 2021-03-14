
<?php 
    $root = $_SERVER['DOCUMENT_ROOT'];
	
	include_once "$root/database/connect.php";
	$configs = include "$root/database/config.php";

	$database = new Connection();
	$database->connect($configs->host, $configs->username, $configs->password, $configs->database);
	
	include_once "$root/database/database.php";
	$db = new Database($database->getConnection());

    // Set Variables
	$user = getUser();
	$season = getSeason();
	$weekNum = getWeekNum();
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
                	echo $resultWeekGames->gameWeekMatchesHTML(2020, $weekNum);
				}
				else {
					$playerWeekGames = new PlayerWeekGames($database->getConnection(), $user->getName());
					echo $playerWeekGames->gameWeekMatchesHTML(2020, $weekNum);
				}
            ?>
        </div>

		<div id="content" class="groupScores">
			<?php include_once "$root/scores/leaderboard.php"; ?>
		</div>

		<div id="content" class="league">
			<?php include_once "$root/table.php"; ?>
		</div>
    </div>
</body>

</HTML>

<?php
	function getUser(): ?User
	{
		global $db;
		
		if (isset($_GET['user'])) {
			$user = $db->getUser($_GET['user']);
			
			if (is_null($user))
				$user = $db->getUser("Results");
		}
		else 
			$user = $db->getUser("Results");
		
		return $user;
	}


	function getSeason()
	{
		$season = $_GET['season'];
		return isset($season) ? intval($season) : 2020;
	}


	function getWeekNum()
	{
		$weekNum = $_GET['weekNum'];
		return isset($weekNum) ? intval($weekNum) : 0;
	}
?>
