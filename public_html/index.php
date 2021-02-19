
<?php 
	include_once __DIR__ . "/database/database.php";
	$db = new Database();
?>

<!DOCTYPE html>
<HTML>

<head>
	<title> Football Scores </title>
	
	<meta charset="utf-8" />
	
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />	
	
	<?php include_once __DIR__ . '/icons.php'; ?>
	
	<link href="/style/page_v1.1.0.css" type="text/css" rel="stylesheet" />
	<link href="/style/home_v1.1.0.css" type="text/css" rel="stylesheet" />
	<link href="/style/games_v1.1.0.css" type="text/css" rel="stylesheet" />
</head>

<body>
	<?php include_once __DIR__ . '/nav_bar.php'; ?>

	<!-- ========== Page main body ========== -->
	<div id="page">
		<div id="content" class="main"> 
			<div class="title">
				<h2> Current Week </h2>
			</div>
			
			<hr />

			<?php
				include_once __DIR__ . '/weekGames.php';
				echo (new ResultWeekGames())->gameWeekMatchesHTML(2020);
			?>
        </div>

		<div id="content" class="groupScores">
			<?php include_once 'leaderboard.php'; ?>
		</div>

		<div id="content" class="league">
			<?php include_once 'table.php'; ?>
		</div>
    </div>
</body>
</HTML>
