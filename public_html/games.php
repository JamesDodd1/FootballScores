
<?php 
	include_once "database/database.php";
	$db = new Database();

    // Set Variables
	$user = getUser();
	
	$season = 2020;
?>

<!DOCTYPE html>
<HTML>

<head>
	<title> Football Scores - <?php echo $user->getName(); ?> </title>
	
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
	<div id='page'>
		<h1 class="name"> 
			<?php echo $user->getName(); ?> 
		</h1>


		<div id="content" class="main"> 
			<?php include_once "weekNumSelector.php"; ?>

			<hr />

            <?php
                include_once __DIR__ . '/weekGames.php';

				if ($user->getIsAnswers())
                	echo (new ResultWeekGames())->gameWeekMatchesHTML(2020, $weekNum);
				else
					echo (new PlayerWeekGames($user->getName()))->gameWeekMatchesHTML(2020, $weekNum);
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

<?php
	function getUser(): ?User
	{
		global $db;
		
		if (isset($_REQUEST['user']))
		{
			$user = $db->getUser($_REQUEST['user']);
			
			if (is_null($user))
				$user = $db->getUser("Results");
		}
		else 
			$user = $db->getUser("Results");
		
		return $user;
	}
?>
