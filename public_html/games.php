
<!DOCTYPE html>
<HTML>

<?php 
	include_once "database/database.php";
	$db = new Database();

	// Set Variables
	$user = getUser();
	
	$season = 2020;
	$lastWeek = $db->totalWeeks($season);
	
	$week = getWeek();
	$week->setMatches($db->weekGames($season, $week->getWeekNum(), $user->getName()));
	
	$scoreSet = scoresComplete();
	
	$today = new DateTime("now", new DateTimeZone('Europe/London')); // Current Date
	//$today = new DateTime("2020-01-01"); 
	
	submitClicked();
?>

<head>
	<title> Football Scores - <?php $user->getName() ?> </title>
	
	<meta charset="utf-8" />
	
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />	
	
	<?php include_once 'icons.php'; ?>
	
	<link href="style/page_v1.1.0.css" type="text/css" rel="stylesheet" />
	<link href="style/home_v1.1.0.css" type="text/css" rel="stylesheet" />
	<link href="style/games_v1.1.0.css" type="text/css" rel="stylesheet" />
</head>


<body>
	
	<?php include_once "nav_bar.php"; // Naviagation bar ?>
	
	
	<!-- ========== Page main body ========== -->
	<div id='page'>

		<!-- Person's name -->
		<h1 class="name"> 
			<?php echo $user->getName(); ?> 
		</h1>


		<div id="content" class="main"> 

			<!-- Header -->
			<ul id="weekNum">
				<?php
					if (!$user->getIsAnswers()) {
						$weekScore = $db->getWeekScore($season, $week->getWeekNum(), $user->getName());

						$start = ($week->getStart())->format('jS M');
						echo "<li class='weekScore'>";
						echo "<b> Score: " . $weekScore . " </b>";
						echo "</li>";

						$col = "secondCol";
					}
				?>
				<li class="weekSelect <?php echo $col; ?>">
					<form method='POST'>

						<!-- Previous week arrow -->
						<?php 
							$arrow = arrow(false); // Get left arrow properties 
							$value = $arrow["value"];
							$enable = $arrow["enabled"];
							$source = $arrow["source"];
						?>
						<button type='submit' class='arrowButton' name='weekArrow' <?php echo $value . " " . $enable; ?>>
							<img class='arrow' <?php echo $source; ?>' alt='Disabled Left Arrow' style='transform: rotate(180deg);' />
						</button>


						<!-- Week selector -->
						<select class='weekCombo' name='week' onchange='this.form.submit()'>
							<?php
								// Loop for each week
								for ($i = 1; $i <= $lastWeek; $i++) 
								{
									// If this week is selected
									$isSelected = $week->getWeekNum() == $i ? "selected" : "";
										
									echo "<option value='$i' $isSelected>Week $i</option>";
								}
							?>
						</select>


						<!-- Next week arrow -->
						<?php 
							$arrow = arrow(true); // Get right arrow properties 
							$value = $arrow["value"];
							$enable = $arrow["enabled"];
							$source = $arrow["source"];
						?>
						<button type='submit' class='arrowButton' name='weekArrow' <?php echo $value . " " . $enable; ?>>
							<img class='arrow' <?php echo $source; ?>' alt='Disabled Right Arrow' />
						</button>

					</form>
				</li>
			</ul>

			<hr />

			<div id="fixtures" style="width: 100%;">
				<form method="POST">
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

						if ($user->getIsAnswers())
							$padding = "padding";
					?>
					<ul class="match <?php echo getStatus($match) . " game-$game" ?> clear">
						<li class="form-arrow" onclick="toggleForm(<?php echo $game; ?>)">
							<img class="close" src="images/downArrow.png" alt="Down arrow" />
						</li>

						<li class="home" onclick="toggleForm(<?php echo $game; ?>)">
							<p> 
								<span class='abbrView' style="display: none;"> <?php echo $match->getHomeTeam()->getAbbreviate(); ?> </span>
								<span class='smallView' style="display: block;"> <?php echo $match->getHomeTeam()->getName(); ?> </span>
								<span class='largeView' style="display: none;"> <?php echo $match->getHomeTeam()->getFullName(); ?> </span>
							</p> 


							<?php 
								$club = $match->getHomeTeam()->getFullName();
								$clubForm = $db->getClubsForm($club, new DateTime($kickOff["time"]));

								if (!is_null($clubForm)) {
									echo "<div class='recent clubForm-$game $edge' style='display: none;'>";

									foreach ($clubForm as $recentForm) {
										if ($club == $recentForm->HomeFull) {
											$opAbbr = $recentForm->AwayAbbr;
											$opName = $recentForm->AwayName;
											$opFull = $recentForm->AwayFull;

											$loc = "(H)";

											if ($recentForm->HomeScore > $recentForm->AwayScore) {
												$result = "win";
												$icon = "W";
											}
											else if ($recentForm->HomeScore == $recentForm->AwayScore) {
												$result = "draw";
												$icon = "D";
											}
											else if ($recentForm->HomeScore < $recentForm->AwayScore) {
												$result = "lose";
												$icon = "L";
											}
										}
										else {
											$opAbbr = $recentForm->HomeAbbr;
											$opName = $recentForm->HomeName;
											$opFull = $recentForm->HomeFull;

											$loc = "(A)";

											if ($recentForm->HomeScore < $recentForm->AwayScore) {
												$result = "win";
												$icon = "W";
											}
											else if ($recentForm->HomeScore == $recentForm->AwayScore) {
												$result = "draw";
												$icon = "D";
											}
											else if ($recentForm->HomeScore > $recentForm->AwayScore) {
												$result = "lose";
												$icon = "L";
											}
										}
										
										echo "
										<hr />
										<div class='form'>
											<p>
												<span class='abbr' style='display: none;'> $opAbbr </span>
												<span class='norm' style='display: none;'> $opName </span>
												<span class='full' style='display: inline-block;'> $opFull </span>
												$loc 
											</p><p> " . $recentForm->HomeScore . " - " . $recentForm->AwayScore . " </p>
											<p class='result $result'> $icon </p>
										</div>";
									}
									echo "</div>";
								}
							?>
						</li>

						<?php 
							$scores = scores($match); 
							$home = $scores["home"];
							$separator = $scores["separator"];
							$away = $scores["away"];
							
							echo "<li> $home </li>";
							echo "<li class='dash'> $separator </li>";
							echo "<li> $away </li>"; 
						?>


						<li class="away <?php echo $padding; ?>" onclick="toggleForm(<?php echo $game; ?>)">
							<p> 
								<span class='abbrView' style="display: none;"> <?php echo $match->getAwayTeam()->getAbbreviate(); ?> </span>
								<span class='smallView' style="display: block;"> <?php echo $match->getAwayTeam()->getName(); ?> </span>
								<span class='largeView' style="display: none;"> <?php echo $match->getAwayTeam()->getFullName(); ?> </span>
							</p> 

							
							<?php 
								$club = $match->getAwayTeam()->getFullName();
								$clubForm = $db->getClubsForm($club, new DateTime($kickOff["time"]));

								if (!is_null($clubForm)) {
									echo "<div class='recent clubForm-$game $edge' style='display: none;'>";

									foreach ($clubForm as $recentForm) {
										if ($club == $recentForm->HomeFull) {
											$opAbbr = $recentForm->AwayAbbr;
											$opName = $recentForm->AwayName;
											$opFull = $recentForm->AwayFull;

											$loc = "(H)";

											if ($recentForm->HomeScore > $recentForm->AwayScore) {
												$result = "win";
												$icon = "W";
											}
											else if ($recentForm->HomeScore == $recentForm->AwayScore) {
												$result = "draw";
												$icon = "D";
											}
											else if ($recentForm->HomeScore < $recentForm->AwayScore) {
												$result = "lose";
												$icon = "L";
											}
										}
										else {
											$opAbbr = $recentForm->HomeAbbr;
											$opName = $recentForm->HomeName;
											$opFull = $recentForm->HomeFull;

											$loc = "(A)";

											if ($recentForm->HomeScore < $recentForm->AwayScore) {
												$result = "win";
												$icon = "W";
											}
											else if ($recentForm->HomeScore == $recentForm->AwayScore) {
												$result = "draw";
												$icon = "D";
											}
											else if ($recentForm->HomeScore > $recentForm->AwayScore) {
												$result = "lose";
												$icon = "L";
											}
										}
										echo "
										<hr />
										<div class='form'>
											<p class='result $result'> $icon </p>
											<p> " . $recentForm->HomeScore . " - " . $recentForm->AwayScore . " </p><p> 
												$loc 
												<span class='abbr' style='display: none;'> $opAbbr </span>
												<span class='norm' style='display: none;'> $opName </span>
												<span class='full' style='display: inline-block;'> $opFull </span>
											</p>
										</div>";
									}
									echo "</div>";
								}
							?>
						</li>


						<?php 
							if (!$user->getIsAnswers()) {
								$joker = joker($match);
								$class = $joker["class"];
								$align = $joker["align"];
								$jokerImage = $joker["joker"];
							
								echo "<li class='joker $class'>";
									echo $jokerImage;
								echo "</li>";
							}
						?>
						
					</ul>
						
					<?php
					} 
						echo "</div>";
					

						// If not viewing Results and current week hasn't started
						if (!$user->getIsAnswers() && $today < $week->getStart()) {
						?>

							<!-- Current displayed week -->
							<input type='hidden' name='week' value='<?php echo $week->getWeekNum(); ?>' /> 
							
							
							<!-- Submit button which toggles between set and edit -->
							<div class='submit'>
								<hr />
								
								<?php 
								// if scores not set
								if (!$scoreSet) 
									echo "<input type='submit' class='submitButton' name='save' value='Set Scores' />";
								else if ($scoreSet) 
									echo "<input type='submit' class='submitButton' name='edit' value='Edit Scores' />";
								
							echo "</div>";
						}
					?>
				</form>
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

<script type='text/javascript'>
	function toggleForm(game) {
		var elements = document.getElementsByClassName("clubForm-" + game);
		var match = document.getElementsByClassName("game-" + game)[0];

		if (elements[0].style.display == "block") {
			elements[0].style.display = "none";
			elements[1].style.display = "none";
		}
		else {
			closeAllForm();

			elements[0].style.display = "block";
			elements[1].style.display = "block";
		}

		match.classList.toggle("shading");
		match.classList.toggle("clear");

		rotateArrow(game);
	}

	function rotateArrow(game) {
		var match = document.getElementsByClassName("game-" + game)[0];
		var arrow = match.getElementsByClassName("form-arrow")[0].getElementsByTagName("img")[0];

		arrow.classList.toggle("open");
		arrow.classList.toggle("close");
	}


	function closeAllForm() {
		var elements = document.getElementsByClassName("recent");
		
		for (var i = 0; i < elements.length; i++) {
			if (elements[i].style.display != "none") 
				elements[i].style.display = "none";
		}

		var matches = document.getElementsByClassName("match");
		for (var i = 0; i < matches.length; i++) {
			if (matches[i].classList.contains("shading")) {
				matches[i].classList.remove("shading");
				matches[i].classList.add("clear");
			}

			var arrow = matches[i].getElementsByClassName("form-arrow")[0].getElementsByTagName("img")[0];
			if (arrow.classList.contains("open")) {
				arrow.classList.remove("open");
				arrow.classList.add("close");
			}
		}
	}


    window.addEventListener("resize", updateTextDisplay);
	updateTextDisplay();

	var minWidth = 750;
	function updateTextDisplay() {
        var fixtures = document.getElementById("fixtures");
		var matches = document.getElementsByClassName("match");

		var matchWidths = [];
		for (var i = 0; i < matches.length; i++) {
			var width = 0;
			var cells = matches[i].getElementsByTagName("li");
			for (var j = 0; j < cells.length; j++) {
				width += cells[j].clientWidth;
			}
			matchWidths.push(width);
		}

        //alert("Fixtures width: " + fixtures.clientWidth + "\n" + "Match 1 width: " + matchWidths[0]); 


		var fullName = true;
		var normName = true;
		var abbrName = true; 
		for (var i = 0; i < matches.length; i++) {
			if (matchWidths[i] < 750 || matchWidths[i] != (fixtures.clientWidth - 20)) {
				full = false;
			}
			else {

			}
		}
		
		/*
		var a = matches[0].getElementsByClassName("abbr");
		for (var b = 0; b < a.length; b++) {
			a[b].style.display = "block";
			if (a[b].value != null) {
				console.log(a[b].style.display);
			}
			else {
				//console.log(a[b].style.display);
			}
		}

		document.getElementsByClassName("full")[0].style.display = "none";
		*/
		
		fullName = false;
		normName = false;
		for (var i = 0; i < matches.length; i++) {
			var abbr = matches[i].getElementsByClassName("abbr");
			var norm = matches[i].getElementsByClassName("norm");
			var full = matches[i].getElementsByClassName("full");

			for (var j = 0; j < full.length; j++) {
				if (fullName) {
					full[j].style.display = "inline-block";
					norm[j].style.display = "none";
					abbr[j].style.display = "none";
				}
				else if (normName) {
					full[j].style.display = "none";
					norm[j].style.display = "inline-block";
					abbr[j].style.display = "none";
				}
				else {
					full[j].style.display = "none";
					norm[j].style.display = "none";
					abbr[j].style.display = "inline-block";
				}
			}
		}
	}
</script>

</HTML>


<?php
	function getUser(): ?User
	{
		global $db;
		
		if (isset($_GET['user']))
		{
			$user = $db->getUser($_GET['user']);
			
			if (is_null($user))
				$user = $db->getUser("Results");
		}
		else 
			$user = $db->getUser("Results");
		
		return $user;
	}


	function getWeek()
	{
		global $db, $season;

		// If a week is selected
		if (isset($_REQUEST['week'])) {
			
			// If week arrow button was clicked
			if (isset($_REQUEST['weekArrow'])) 
				return $db->getCurrentWeek($season, intval($_REQUEST['weekArrow'])); // Chosen week
			else
				return $db->getCurrentWeek($season, intval($_REQUEST['week'])); // Chosen week
		}
		else 
			return $db->getCurrentWeek($season); 

	}


	function arrow(bool $nextWeek)
	{
		global $week, $lastWeek;

		$weekNum = $week->getWeekNum();

		// If right arrow
		if ($nextWeek) 
		{
			$newWeek = $weekNum + 1;
			$finalWeek = $lastWeek;
		}
		// If left arrow
		else 
		{
			$newWeek = $weekNum - 1;
			$finalWeek = 1;
		}
		
		
		// If no more weeks 
		if ($weekNum == $finalWeek) 
		{
			$isEnabled = "disabled";
			$image = "Arrow_Disabled.png";
		}
		else 
		{
			$isEnabled = "";
			$image = "Arrow_Enabled.png";
		}
			
		return ["value" => "value='$newWeek'", "enabled" => $isEnabled, "source" => "src='images/$image'"];
	}


	function getStatus($match)
	{
		// Get status of the scores
		if ($match->getWin()) 
			return "win";
		else if ($match->getDraw()) 
			return "draw";
		else if ($match->getLose()) 
			return "lose";
		
		return "pending";
	}


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


	/* ========== Creates match scores ========== */
	function scores($match) 
	{
		global $user, $week, $scoreSet, $today;

		$weekStart = $week->getStart();
		//$weekEnd = $week->getEnd();
		
		// If on results page
		if ($user->getIsAnswers()) { 

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
		}
		else { // For all other users

			// If week has begun
			if ($weekStart < $today) {
				$homeScore = $match->getHomeScore();
				$awayScore = $match->getAwayScore();

				// Match with a score
				if ($homeScore >= 0 && $awayScore >= 0) {
					$home = "<p> " . $homeScore . " </p>";
					$separator = "<p> - </p>";
					$away = "<p> " . $awayScore . " </p>";
				}
				else {
					$home = "<p> F </p>";
					$separator = "<p> - </p>";
					$away = "<p> F </p>";
				}
			}
			else {

				// If scores haven't be completed
				if (!$scoreSet) {
					$home = scoreSelect($match, true);
					$separator = "";
					$away = scoreSelect($match, false);
				}
				else {
					$home = "<p> " . $match->getHomeScore() . " </p>";
					$separator = "<p> - </p>";
					$away = "<p> " . $match->getAwayScore() . " </p>";
				}
			}
		}

		return ["home" => $home, "separator" => $separator, "away" => $away];
	}


	function scoreSelect($match, bool $homeTeam)
	{
		$selector = "";

		if ($homeTeam) 
		{
			$scoreName = "homeScore".$match->getMatchNum();
			$score = $match->getHomeScore();
		}
		else 
		{
			$scoreName = "awayScore".$match->getMatchNum();
			$score = $match->getAwayScore();
		}

		$selector = $selector . "
		<select class='score' name='$scoreName'> 
			<option value='-1'> </option>";

			$maxScore = 10;
			for ($i = 0; $i <= $maxScore; $i++) 
			{
				// Select score
				$isSelected = $score == $i ? "selected" : "";
				
				$selector = $selector . "<option value='$i' $isSelected> $i </option>";
			}
		
		$selector = $selector . "</select> ";

		return $selector;
	}

	/* ========== Creates the joker ========== */
	function joker($match) 
	{
		global $user;

		// If viewing results
		if ($user->getIsAnswers()) {
			$class = "jokerSpace";
			$joker = "<p> &nbsp </p>";
		}
		else {
			global $scoreSet;
			
			// If in scores not saved
			if (!$scoreSet) {
				$class = "radio";
				$align = "center";
			}
			else 
				$align = "right";

		
			// If joker has been set
			if ($match->getJoker()) 
			{
				$image = "Ball_Blue.png";
				$check = "checked";
			}
			else 
				$image = "Ball_White.png";
			

			$macthNum = $match->getMatchNum();
			
			$input = "<input type='radio' name='joker' id='joker-$macthNum' value='$macthNum' />";
			$image = "<img name='ballImg' src='images/$image' $check />";
			$label = "<label for='joker-$macthNum'> $image </label>";

			$joker = $input . $label;
		}

		return ["class" => $class, "align" => "align:'$align'", "joker" => $joker];
	}


	/* ========== Submit button ========== */
	function submitClicked() 
	{
		global $db, $user, $week, $season;

		// If save submit button clicked
		if (isset($_POST['save'])) {

			for ($i = 1; $i <= count($week->getMatches()); $i++) 
			{
				$match = $week->getMatches()[$i - 1];
				$homeScore = $_REQUEST['homeScore'.$i];
				$awayScore = $_REQUEST['awayScore'.$i];
				$isJoker = null;
				
				if (isset($_POST['joker'])) 
					$isJoker = $_POST['joker'] == $match->getMatchNum() ? true : false;
				
				$db->setScores($user->getName(), $season, $week->getWeekNum(), $match, $homeScore, $awayScore, $isJoker);
			}

			// Refresh page
			echo "<meta http-equiv='refresh' content='0'>";
		}
	}


	function scoresComplete() 
	{
		global $week;

		// Checks if all scoring has been done and whether to start on edit view
		if (!isset($_POST['edit'])) 
		{
			foreach ($week->getMatches() as $match)
			{
				if ($match->getHomeScore() < 0 || $match->getAwayScore() < 0)
					return false;
			}

			return true;
		}

		return false;
	}


	function clubForm($match) 
	{
		
	}


	function view($var)
	{
		echo "<pre>";
		print_r($var);
		echo "</pre>";
	}
?>
