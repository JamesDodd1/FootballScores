
<?php
require_once 'default.php'; 
use Source\Week\ResultWeekGames;
?>

<!DOCTYPE html>
<HTML>

<head>
    <title> Football Scores </title>
    
    <?php include_once RESOURCES.'/head.php'; ?>
</head>

<body>
    <?php include_once RESOURCES."/nav_bar.php"; ?>
    
    <!-- ========== Page main body ========== -->
    <div id="page">
        <div id="content" class="main"> 
            <div class="title">
                <h2> Current Week </h2>
            </div>
            
            <hr />
            
            <?php
                //include_once "$root/games/weekGames.php";
                $resultWeekGames = new ResultWeekGames();
                echo $resultWeekGames->gameWeekMatchesHTML('Premier League', 2020);
            ?>
        </div>
        
        
        <div id="content" class="groupScores">
            <?php
            include_once "$root/scores/leaderboardFactory.php";
            
            $leaderboard = LeaderboardFactory::create("euros", 2021);
            echo $leaderboard->generateHTML();
            ?>
        </div>
        
        <div id="content" class="league">
            <?php
            include_once "$root/table/tableFactory.php";
            
            $table = TableFactory::create("euros", 2021);
            echo $table->generateHTML();
            ?>
        </div>
    </div>
</body>

</HTML>
