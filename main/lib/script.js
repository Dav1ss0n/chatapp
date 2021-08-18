document.addEventListener("DOMContentLoaded", function() {
    userStatusChanger("Online");
    sessionInfoGet();
})
window.onbeforeunload = function() {
    userStatusChanger("Offline");
    return true;
}

function userStatusChanger(status) {
    let xml = new XMLHttpRequest();
    xml.open("POST", "/chat proto/system/statuser.php", true);
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send("status="+status);
}
 
function sessionInfoGet() {
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            sessionChecker(this.response);
        }
    }
    xml.open("POST", "/chat proto/system/session-time.php", true);
    xml.responseType = "json";
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send();
}

function sessionChecker(array) {
    if (array.Parameter == "Session") {
        if (array.Status == "Expired") {
            message.innerText = array.Problem;
            $("#messagerDimmer").fadeIn(90);

            messagerDimmer.addEventListener("click", function() {
                self.location = "http://localhost/chat%20proto";
            })
        } else if (array.Status == "Ok") {
            let lastEntranceID = array.Problem[0];
            let sessionEndTime = array.Problem[1]*1000;
            console.log(sessionEndTime)
    
            let currentTime = new Date().valueOf();
            console.log(currentTime);
            let sessionTimeoutTime = sessionEndTime-currentTime;
    
            console.log(sessionTimeoutTime);
            setTimeout(sessionInfoGet, sessionTimeoutTime)
        }
    }
}