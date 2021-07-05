
window.addEventListener("resize", updateTable);
//updateTable();

function abbrCellText(element) {
    var abbreviated = element.getElementsByClassName("abbr")[0];
    var normal = element.getElementsByClassName("norm")[0];
    var full = element.getElementsByClassName("full")[0];

    abbreviated.style.display = "inline-block";
    normal.style.display = "none";
    full.style.display = "none";
}


var tableMinWidth = 1000;
function updateTable() {
    var leagueTables = document.getElementsByClassName("leagueTable");
    var leagues = document.getElementsByClassName("table");
    //console.log("Matches width: " + leagueTables[0].clientWidth + "\n" + "Table width: " + leagues[0].clientWidth); 
    
    for (var i = 0; i < leagueTables.length; i++) {
        var pos = leagueTables[i].getElementsByClassName("pos");
        var played = leagueTables[i].getElementsByClassName("play");
        var won = leagueTables[i].getElementsByClassName("won");
        var drawn = leagueTables[i].getElementsByClassName("draw");
        var lost = leagueTables[i].getElementsByClassName("lost");
        var goalsFor = leagueTables[i].getElementsByClassName("for");
        var goalsAgainst = leagueTables[i].getElementsByClassName("agst");
        var difference = leagueTables[i].getElementsByClassName("dif");
        var points = leagueTables[i].getElementsByClassName("pts");

        abbrCellText(pos[0]);
        abbrCellText(played[0]);
        abbrCellText(won[0]);
        abbrCellText(drawn[0]);
        abbrCellText(lost[0]);
        abbrCellText(goalsFor[0]);
        abbrCellText(goalsAgainst[0]);
        abbrCellText(difference[0]);
        abbrCellText(points[0]);



        var team = leagueTables[i].getElementsByClassName("team");

        for (var j = 1; j < team.length; j++) {
            abbrCellText(team[j]);
            goalsFor[j].style.display = "none";
            goalsAgainst[j].style.display = "none";
        }
        

        for (var j = 0; j < team.length; j++) {
            goalsFor[j].style.display = "none";
            goalsAgainst[j].style.display = "none";
        }
    }

    return;


    for (var i = 0; i < leagueTables.length; i++) {
        if (leagues[i].clientWidth > 1000 && leagues[i].clientWidth == leagueTables[i].clientWidth) {
            tableHeadings(2);
            teamNames(2);

            // If columns are hidden
            var forCol = leagueTables[i].getElementsByClassName("for");
            var agstCol = leagueTables[i].getElementsByClassName("agst");
            if (forCol[0].style.display == "none" && agstCol[0].style.display == "none") {
                showColumn("for");
                showColumn("agst");
            }
        }
        else if (leagues[i].clientWidth > 750 && leagues[i].clientWidth == leagueTables[i].clientWidth) {
            tableHeadings(1);
            teamNames(1);

            // If columns are hidden
            var forCol = leagueTables[i].getElementsByClassName("for");
            var agstCol = leagueTables[i].getElementsByClassName("agst");
            if (forCol[0].style.display == "none" && agstCol[0].style.display == "none") {
                showColumn("for");
                showColumn("agst");
            }
        }
        else {
            tableHeadings(0);
            teamNames(0);
            
            // If table large enough for all columns to return 
            if (leagues[i].clientWidth >= tableMinWidth/* && leagues[i].clientWidth == matchWidth*/){
                showColumn("for");
                showColumn("agst");
            }
            else {
                tableMinWidth = leagues[i].clientWidth; // Min width table with all columns can be
                
                hideColumn("for");
                hideColumn("agst");
            }
        }
    }
}


function tableHeadings(view) {
    // All league table columns
    var leagueTables = document.getElementsByClassName("leagueTable");

    for (var i = 0; i < leagueTables.length; i++) {
        var cols = [
            leagueTables[i].getElementsByClassName("pos"),
            leagueTables[i].getElementsByClassName("play"),
            leagueTables[i].getElementsByClassName("won"),
            leagueTables[i].getElementsByClassName("draw"),
            leagueTables[i].getElementsByClassName("lost"),
            leagueTables[i].getElementsByClassName("for"),
            leagueTables[i].getElementsByClassName("agst"),
            leagueTables[i].getElementsByClassName("dif"),
            leagueTables[i].getElementsByClassName("pts")
        ];


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
}


function teamNames(view) {
    var leagueTables = document.getElementsByClassName("leagueTable");

    for (var i = 0; i < leagueTables.length; i++) {
        var teams = leagueTables[i].getElementsByClassName("team");


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
}


function showColumn(col_name) {
    var leagueTables = document.getElementsByClassName("leagueTable");

    for (var i = 0; i < leagueTables.length; i++) {
        var all_col = leagueTables[i].getElementsByClassName(col_name);
        console.log(all_col.length);
        for (var i = 0; i < all_col.length; i++) {
            all_col[i].style.display = "table-cell";
        }
    }
}


function hideColumn(col_name) {
    var leagueTables = document.getElementsByClassName("leagueTable");

    for (var i = 0; i < leagueTables.length; i++) {
        var all_col = leagueTables[i].getElementsByClassName(col_name);

        for (var i = 0; i < all_col.length; i++) {
            all_col[i].style.display = "none";
        }
    }
}

