
<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	
	include_once "$root/database/connect.php";
	$configs = include "$root/database/config.php";

	$database = new Connection();
	$database->connect($configs->host, $configs->username, $configs->password, $configs->database);
?>

<!DOCTYPE html>
<HTML>

<head>
	<title> Football Scores </title>
	
	<meta charset="utf-8" />
	
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />	
	
	<?php include_once "$root/icons.php"; ?>
	
	<link href="/style/page_v1.1.0.css" type="text/css" rel="stylesheet" />
	<link href="/style/home_v1.1.0.css" type="text/css" rel="stylesheet" />
	<link href="/style/games_v1.1.0.css" type="text/css" rel="stylesheet" />
</head>

<body>
	<?php include_once "$root/nav_bar.php"; ?>

	<!-- ========== Page main body ========== -->
	<div id="page">
		<div id="content" class="main"> 
			<div class="title">
				<h2> Current Week </h2>
			</div>
			
			<hr />

			<?php
				include_once "$root/games/weekGames.php";
				$resultWeekGames = new ResultWeekGames($database->getConnection());
				echo $resultWeekGames->gameWeekMatchesHTML(2020);
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
