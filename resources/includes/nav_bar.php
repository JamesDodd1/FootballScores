
<nav class="nav">
    <a href="/"> Home </a>
    <div class="dropdown">
        <button class="dropbtn"> Competition
            <img src="<?php echo IMAGES; ?>/downArrow.png" alt="Drop down arrow" />
        </button>
        <div class="dropdown-content">
            <a onclick="updateCompetition()"> Premier League </a>
            <a onclick="updateCompetition()"> Euros </a>
        </div>
    </div>
    <div class="dropdown">
        <button class="dropbtn"> Players
            <img src="<?php echo IMAGES; ?>/downArrow.png" alt="Drop down arrow" />
        </button>
        <div class="dropdown-content">
            <?php echo usersHTML(); ?>
        </div>
    </div>
    <a href="/games/Results"> Results </a>
    <a href="/scores"> Scores </a>
</nav>

<?php
use Source\User\UserService;
function usersHTML() {
    $userService = new UserService;
    $users = $userService->getAllUsers();

	if (is_null($users)) { return; }
    
    $userLink = "";
    foreach ($users as $user)
    {
        $name = $user->getName();
        $userLink .= "<a href='/games/$name'> $name </a> \n";
    }
    
    return $userLink;
}
?>

<script>
	console.log("Start\n");
	function updateCompetition() {
		console.log("Updated\n");
	}
</script>
