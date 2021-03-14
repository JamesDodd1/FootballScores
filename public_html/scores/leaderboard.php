
<div id="leaderboard">
    <h2> Leaderboard </h2>

    <hr />

    <table>
        <tr class="header">
            <th> </th>
            <th colspan="2"> <b> Name </b> </th>
            <th> <b> Pts </b> </th>
        </tr>

        <?php
        $root = $_SERVER['DOCUMENT_ROOT'];
        include_once "$root/database/database.php";

        $pos = 1;
        $season = "2020";
        $db = new Database($database->getConnection());
        
        foreach ($db->leaderboard($season) as $player) {
            echo "
            <tr class='player'>
                <td class='pos'> <p> " . $pos++ . " </p> </td>
                <td class='name'> <p> $player->Name </p> </td>
                <td class='weekPts'> <p> +$player->WeekScore </p> </td>
                <td class='totalPts'> <p> $player->SeasonScore </p> </td>
            </tr>";
        }
        ?>
    </table>
</div>
