
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
