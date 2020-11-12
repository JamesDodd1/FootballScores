
<!DOCTYPE html>

<HTML>

<head>
	<title> Football Scores </title>
	
	<meta charset="utf-8" />
	
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />	
	
	<?php include_once 'icons.php'; ?>
	
	<link href="style/page_v1.1.0.css" type="text/css" rel="stylesheet" />
	<link href="style/home_v1.1.0.css" type="text/css" rel="stylesheet" />
	<link href="style/games_v1.1.0.css" type="text/css" rel="stylesheet" />
</head>

<?php 
	include_once "database/database.php";
	$db = new Database();

	// Set Variables
	$user = $db->getUser("Results");
	
	$season = 2020;
	//$lastWeek = $db->totalWeeks($season);
	
	$week = $db->getCurrentWeek($season);
	$week->setMatches($db->weekGames($season, $week->getWeekNum(), $user->getName()));

	//$scoreSet = scoresComplete();
	
	$today = new DateTime("now", new DateTimeZone('Europe/London')); // Current Date
	//$today = new DateTime("2020-01-01"); 

	function getKickOff($match, DateTime $lastKickOff) 
	{
		$kickOff = $match->getKickOff();

		// If match is on a new day
		if ($kickOff->format('j') != $lastKickOff->format('j')) 
		{
			$style = "style='padding-right: 0;'";
			$kickOffDay = $kickOff->format('l jS F');
		}

		return ["style" => $style, "day" => $kickOffDay, "time" => $kickOff->format('Y-m-d H:i:s')];
	}

	function scores($match) 
	{
		global $week, $today;

		$weekStart = $week->getStart();

		// If week has begun
		if ($weekStart < $today) {

			// Match with a score
			if ($match->getHomeScore() >= 0 && $match->getAwayScore() >= 0) {
				$home = "<p> " . $match->getHomeScore() . " </p>";
				$separator = "<p> - </p>";
				$away = "<p> " . $match->getAwayScore() . " </p>";
			}
			else { // Match without a score
				$home = "";
				$separator = "<p> " . $match->getKickOff()->format("H:i") . " </p>";
				$away = "";
			}
		}
		else { // Future matches 
			$home = "";
			$separator = "<p> " . $match->getKickOff()->format("H:i") . " </p>";
			$away = "";
		}
		

		return ["home" => $home, "separator" => $separator, "away" => $away];
	}
?>

<body>
	
	<?php 
		include_once __DIR__ . '/database/database.php';
		$db = new Database();

		// Naviagation bar	
		include_once 'nav_bar.php'; 
	?>


	<!-- ========== Page main body ========== -->
	<div id="page">
		<div id="content" class="main"> 
			<div class="title">
				<h2> Current Week </h2>
			</div>
			
			<hr />

			<div id="fixtures" style="width: 100%;">
				<?php
				if ($user->getIsAnswers()) 
					$edge = "edge";

				$game = 0;
				foreach ($week->getMatches() as $match)
				{ 
					++$game;
					
					$kickOff = getKickOff($match, new DateTime($kickOff["time"]));

					$style = $kickOff["style"];
					$day = $kickOff["day"];
					$time = (new DateTime($kickOff["time"]))->format('H:i');
					
					if (!is_null($day)) {
						if ($game != 1) {
							echo "</div>";
						}
						
						echo "<div class='day'>";
						echo "<h3> $day </h3>";
					}
					
					if (!$user->getIsAnswers()) 
						$padding = "padding";
				?>
				<ul class="match <?php echo "game-$game" ?> clear">
					<li class="home <?php echo $padding; ?>">
						<p> 
							<span class='abbrView' style="display: none;"> <?php echo $match->getHomeTeam()->getAbbreviate(); ?> </span>
							<span class='smallView' style="display: block;"> <?php echo $match->getHomeTeam()->getName(); ?> </span>
							<span class='largeView' style="display: none;"> <?php echo $match->getHomeTeam()->getFullName(); ?> </span>
						</p>
					</li>

					<?php 
						$scores = scores($match); 
						$home = $scores["home"];
						$separator = $scores["separator"];
						$away = $scores["away"];
						
						echo "<li> $home </li>";
						echo "<li> $separator </li>";
						echo "<li> $away </li>"; 
					?>


					<li class="away">
						<p> 
							<span class='abbrView' style="display: none;"> <?php echo $match->getAwayTeam()->getAbbreviate(); ?> </span>
							<span class='smallView' style="display: block;"> <?php echo $match->getAwayTeam()->getName(); ?> </span>
							<span class='largeView' style="display: none;"> <?php echo $match->getAwayTeam()->getFullName(); ?> </span>
						</p>
					</li>
				</ul>
					
				<?php
				} 
					echo "</div>";
				?>
			</div>
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
