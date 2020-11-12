
<!-- ========== Navigation Bar ========== -->
<div class="nav">
	<a href="/"> Home </a>
	<div class="dropdown">
		<button class="dropbtn"> Players
			<img src="images/downArrow.png" alt="Down arrow" />
		</button>
		<div class="dropdown-content">
			<?php 
				foreach ($db->getAllUsers(false) as $users)
				{
					$name = $users->getName();
					echo "<a href='games?user=$name'> $name </a>";
				}
			?>
		</div>
	</div>
	<a href="games"> Results </a>
	<a href="scores"> Scores </a>
</div>
