
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
        $pos = 1;
        $season = "2020";
        /*
        $seasonScores = $db->getSeasonScore($season);
        usort($seasonScores, function($a, $b) {
            if (($a->Score - $b->Score) < 0)
                return $b->Score - $a->Score;
        });
        */
        foreach ($db->leaderboard($season) as $player) {
            echo "
            <tr class='player'>
                <td class='pos'> <p> " . $pos++ . " </p> </td>
                <td class='name'> <p> " . $player->Name . " </p> </td>
                <td class='weekPts'> <p> +" . $player->WeekScore . " </p> </td>
                <td class='totalPts'> <p> " . $player->SeasonScore . " </p> </td>
            </tr>";
        }
        ?>
    </table>
</div>
