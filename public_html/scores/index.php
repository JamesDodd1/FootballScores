
<?php $root = $_SERVER['DOCUMENT_ROOT']; ?>

<!DOCTYPE html>
<HTML>

<head>
	<title>Football Scores</title>
	
	<meta charset="utf-8" />
	
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />	
	
	<?php include_once "$root/icons.php"; ?>
	
	<link href="/style/page_v1.1.0.css" type="text/css" rel="stylesheet" />
	<link href="/style/scores_v1.1.0.css" type="text/css" rel="stylesheet" />
</head>

<body>
	<?php include_once "$root/nav_bar.php"; ?>
	
	
	<!-- ========== Page main body ========== -->
	<div id="page">

		<h1>Scores</h1>

		<!-- Scores Table -->
		<div id='content'>
			<table id='points'>
				<colgroup>
					<col style='width: 8em;'>
					<col> <col style='width: 2.5em;'> <col>
					<col> <col style='width: 2.5em;'> <col>
					<col> <col style='width: 2.5em;'> <col>
					<col> <col style='width: 2.5em;'> <col>
					<col> <col style='width: 2.5em;'> <col>
				</colgroup>


				
				<!-- Players Names -->
				<tr>
					<th> </th>
					<?php
						include_once "$root/database/database.php";
						$db = new Database();
		
						// Loop through each person
						foreach ($db->getAllUsers(false) as $user) 
						{
							echo "<th colSpan='3'> <b>" . $user->getName() . "</b> </th>";
						}
					?>
				</tr>
				

				<!-- Weekly Scores -->
				<?php
					$season = 2020;
					foreach ($db->getWeekScores($season) as $weekScore) 
					{
						$weekCount = count($weekScore);

						// Create array with empty zero scores
						for ($i = 0; $i < $weekCount - 1; $i++)
						{
							$currentTotal[] = 0;
						}

						echo "
						<tr>
							<td class='weekNum'> <b>Week " . $weekScore[0] . "</p> </td>";

							for ($i = 1; $i < $weekCount; $i++)
							{
								$currentTotal[$i - 1] += $weekScore[$i];

								echo "
								<td class='previous'> <p>+" . $weekScore[$i] . "</p> </td>
								<td class='equals'> <p>=></p> </t>
								<td class='total'> <p>" . $currentTotal[$i - 1] . "</p> </td>";
							}

						echo "</tr>";
					} 
				?>


				<tr class='line' style='border-bottom-style: hidden; background-color: white;'> <td colSpan='16'> <hr> </td> </tr>


				<!-- Total Scores -->
				<tr style='border-top-style: hidden; background-color: white;'>
					<td class='weekNum'> <b>Total</b> </td>

					<?php	
						foreach ($db->getSeasonScore($season) as $seasonScore)
						{
							echo "<td colSpan='3' style='text-align: center;'> <b>" . $seasonScore->Score . "</b> </td>";
						}
					?>
				</tr>
			</table>
		</div>
	</div>
	
</body>

</HTML>
