
<div class="nav">
	<a href="/"> Home </a>
	<div class="dropdown">
		<button class="dropbtn"> Players
			<img src="/images/downArrow.png" alt="Down arrow" />
		</button>
		<div class="dropdown-content">
			<?php echo usersHTML(); ?>
		</div>
	</div>
	<a href="/games/Results"> Results </a>
	<a href="/scores"> Scores </a>
</div>

<?php
	function usersHTML() {
        $root = $_SERVER['DOCUMENT_ROOT'];
		include_once "$root/database/database.php";
		$db = new Database();

		$userDropDown = "";
		foreach ($db->getAllUsers(false) as $users)
		{
			$name = $users->getName();
			$userDropDown .= "<a href='/games/$name'> $name </a>";
		}

		return $userDropDown;
	}
?>
