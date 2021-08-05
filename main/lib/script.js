$(document).ready(function() {
    
})

function Ajaxer(param) {
    let xml = new XMLHttpRequest();
    if(param == "sessionChecker") {
        xml.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                
            }
        }

    xml.open("POST", "/system/log.php");
    xml.responseType = "json";
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send("param="+param);
    }
}

function sessionChecker(array) {
    
}