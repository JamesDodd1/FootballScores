
<div id='leagueTable'>
    <h2> Premier League </h2>

    <hr />
    
    <table>
        <tr class="header">
            <th class='pos'> 
                <b> 
                    <span class='abbr'>  	     </span>
                    <span class='norm'> Pos      </span>
                    <span class='full'> Position </span>
                </b> 
            </th>
            <th class='team'> 
                <b> Team </b> 
            </th>
            <th class='play'> 
                <b> 
                    <span class='abbr'> P      </span>
                    <span class='norm'> Played </span>
                    <span class='full'> Played </span>
                </b> 
            </th>
            <th class='won'> 
                <b> 
                    <span class='abbr'> W   </span>
                    <span class='norm'> Won </span>
                    <span class='full'> Won </span>
                </b> 
            </th>
            <th class='draw'> 
                <b> 
                    <span class='abbr'> D     </span>
                    <span class='norm'> Drawn </span>
                    <span class='full'> Drawn </span>
                </b> 
            </th>
            <th class='lost'> 
                <b> 
                    <span class='abbr'> L    </span>
                    <span class='norm'> Lost </span>
                    <span class='full'> Lost </span>
                </b> 
            </th>
            <th class='for'> 
                <b> 
                    <span class='abbr'> GF		  </span>
                    <span class='norm'> For 	  </span>
                    <span class='full'> Goals For </span>
                </b> 
            </th>
            <th class='agst'>  
                <b> 
                    <span class='abbr'> GA	          </span>
                    <span class='norm'> Against 	  </span>
                    <span class='full'> Goals Against </span>
                </b> 
            </th>
            <th class='dif'> 
                <b> 
                    <span class='abbr'> GD               </span>
                    <span class='norm'> Differnece 	     </span>
                    <span class='full'> Goals Difference </span>
                </b> 
            </th>
            <th class='pts'> 
                <b> 
                    <span class='abbr'> Pts    </span>
                    <span class='norm'> Points </span>
                    <span class='full'> Points </span>
                </b> 
            </th>
        </tr>

        <?php
            $root = $_SERVER['DOCUMENT_ROOT'];
            include_once "$root/database/database.php";
            $db = new Database($database->getConnection());

            $league = $db->getLeagueTable();
            $positions = $league->getPositions();
            $cl_Pos = $league->getChampionsLeaguePos();
            $rel_Pos = $league->getRelegationPos();

            $pos = 1;
            foreach ($positions as $position)
            {
                $line = ($pos == $cl_Pos || $pos == $rel_Pos) ? " line" : null;
                ?>
                
                <tr class="row<?php echo $line; ?>">
                    <td class="pos"> 
                        <p> <?php echo $pos++; ?> </p> 
                    </td>

                    <td class="team"> 
                        <b>
                            <span class='abbr'> <?php echo $position->getClub()->getAbbreviate(); ?> </span>
                            <span class='norm'> <?php echo $position->getClub()->getName(); ?>       </span>
                            <span class='full'> <?php echo $position->getClub()->getFullName(); ?>   </span>
                        </b> 
                    </td>

                    <td class="play"> 
                        <p> <?php echo $position->getPlayed(); ?> </p> 
                    </td>

                    <td class="won">  
                        <p> <?php echo $position->getWon(); ?> </p> 
                    </td>

                    <td class="draw"> 
                        <p> <?php echo $position->getDrawn(); ?> </p> 
                    </td>

                    <td class="lost"> 
                        <p> <?php echo $position->getLost(); ?> </p> 
                    </td>

                    <td class="for"> 
                        <p> <?php echo $position->getGoalsFor(); ?> </p> 
                    </td>

                    <td class="agst"> 
                        <p> <?php echo $position->getGoalsAgainst(); ?> </p> 
                    </td>

                    <td class="dif"> 
                        <p> <?php echo ($gd = $position->getGoalDifference()) > 0 ? "+$gd" : $gd; ?> </p> 
                    </td>

                    <td class="pts"> 
                        <p> <?php echo $position->getPoints(); ?> </p> 
                    </td>
                </tr>

                <?php 
            }

        ?>
    </table>
</div>
        
<script type='text/javascript'> 
    window.addEventListener("resize", updateTable);
    updateTable();
    

    var tableMinWidth = 1000;
    function updateTable() {
        var leagueTable = document.getElementById("leagueTable");
        var league = document.getElementsByTagName("table")[0];
        //alert("Matches width: " + leagueTable.clientWidth + "\n" + "Table width: " + league.clientWidth); 
        

        if (league.clientWidth > 1000 && league.clientWidth == leagueTable.clientWidth) {
            tableHeadings(2);
            teamNames(2);

            // If columns are hidden
            var forCol = leagueTable.getElementsByClassName("for");
            var agstCol = leagueTable.getElementsByClassName("agst");
            if (forCol[0].style.display == "none" && agstCol[0].style.display == "none") {
                showColumn("for");
                showColumn("agst");
            }
        }
        else if (league.clientWidth > 750 && league.clientWidth == leagueTable.clientWidth) {
            tableHeadings(1);
            teamNames(1);

            // If columns are hidden
            var forCol = leagueTable.getElementsByClassName("for");
            var agstCol = leagueTable.getElementsByClassName("agst");
            if (forCol[0].style.display == "none" && agstCol[0].style.display == "none") {
                showColumn("for");
                showColumn("agst");
            }
        }
        else {
            tableHeadings(0);
            teamNames(0);
            
            // If table large enough for all columns to return 
            if (league.clientWidth >= tableMinWidth && league.clientWidth == matchWidth){
                showColumn("for");
                showColumn("agst");
            }
            else {
                tableMinWidth = league.clientWidth; // Min width table with all columns can be

                hideColumn("for");
                hideColumn("agst");
            }
        }
    }
    

    function tableHeadings(view) {
        // All league table columns
        var leagueTable = document.getElementById("leagueTable");
        var cols = [leagueTable.getElementsByClassName("pos"),
                    leagueTable.getElementsByClassName("play"),
                    leagueTable.getElementsByClassName("won"),
                    leagueTable.getElementsByClassName("draw"),
                    leagueTable.getElementsByClassName("lost"),
                    leagueTable.getElementsByClassName("for"),
                    leagueTable.getElementsByClassName("agst"),
                    leagueTable.getElementsByClassName("dif"),
                    leagueTable.getElementsByClassName("pts")];


        // Set text type view status
        if (view == 0) { // Abrreviated text
            var abbrView = "inline-block";
            var normView = "none";
            var fullView = "none";
        }
        else if (view == 1) { // Shortened text
            var abbrView = "none";
            var normView = "inline-block";
            var fullView = "none";
        }
        else { // Full text
            var abbrView = "none";
            var normView = "none";
            var fullView = "inline-block";
        }


        // Update all column text types
        for (var i = 0; i < cols.length; i++) {
            var col = cols[i];

            if (col[0].getElementsByClassName("abbr") != null) {
                var abbr = col[0].getElementsByClassName("abbr");

                if (abbr[0] != null)
                    abbr[0].style.display = abbrView;
            }
            
            if (col[0].getElementsByClassName("norm") != null)  {
                var norm = col[0].getElementsByClassName("norm");

                if (norm[0] != null)
                    norm[0].style.display = normView;
            }
        

            if (col[0].getElementsByClassName("full") != null)  {
                var full = col[0].getElementsByClassName("full");

                if (full[0] != null)
                    full[0].style.display = fullView;
            }
        }
    }

    
    function teamNames(view) {
        var leagueTable = document.getElementById("leagueTable");
        var teams = leagueTable.getElementsByClassName("team");


        // Set text type view status
        if (view == 0) { // Abrreviated text
            var abbrView = "inline-block";
            var normView = "none";
            var fullView = "none";
        }
        else if (view == 1) { // Shortened text
            var abbrView = "none";
            var normView = "inline-block";
            var fullView = "none";
        }
        else { // Full text
            var abbrView = "none";
            var normView = "none";
            var fullView = "inline-block";
        }

        
        // Update team names
        for (var i = 1; i < teams.length; i++) {
            // Abbreviated name
            if (teams[i].getElementsByClassName("abbr") != null) {
                var abbr = teams[i].getElementsByClassName("abbr");
                
                abbr[0].style.display = abbrView;
            }
            
            // Shortened name
            if (teams[i].getElementsByClassName("norm") != null) {
                var abbr = teams[i].getElementsByClassName("norm");
                
                abbr[0].style.display = normView;
            }

            // Full name
            if (teams[i].getElementsByClassName("full") != null) {
                var abbr = teams[i].getElementsByClassName("full");
                
                abbr[0].style.display = fullView;
            }
        }
    }


    function showColumn(col_name) {
        var leagueTable = document.getElementById("leagueTable");
        var all_col = leagueTable.getElementsByClassName(col_name);

        for (var i = 0; i < all_col.length; i++) {
            all_col[i].style.display = "table-cell";
        }
    }

    
    function hideColumn(col_name) {
        var leagueTable = document.getElementById("leagueTable");
        var all_col = leagueTable.getElementsByClassName(col_name);

        for (var i = 0; i < all_col.length; i++) {
            all_col[i].style.display = "none";
        }
    }
</script>
