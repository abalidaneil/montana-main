i = true
function funny(){
    if (i == true){
        document.getElementById("sidebar").style= "display:none;";
        document.getElementById("main").style = "margin-left:0;";
        i = false
    } else if (i == false){
        document.getElementById("sidebar").style= "display:flex;";
        document.getElementById("main").style = "margin-left:260px;";
        i = true
    }
}