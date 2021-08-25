const message = document.getElementById("message");
const messagerDimmer = document.getElementById("messagerDimmer");

document.addEventListener("DOMContentLoaded", function() {
    userStatusChanger("Online");
    sessionInfoGet();
});

// window.onbeforeunload = null;
window.onbeforeunload = function(e) {
    e.preventDefault();
    userStatusChanger("Offline");
    return null;
}

function userStatusChanger(status) {
    let xml = new XMLHttpRequest();
    xml.open("POST", "/chat proto/system/statuser.php", true);
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send("status="+status);
    document.getElementById("user-status").innerText = status;
    if (status == "Online") {
        const userStatusDot = document.getElementById("user-status-dot");
        userStatusDot.style = "color: #00bb16;"
    }
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
            // self.location = "http://localhost/chat%20proto";
            message.innerText = Object.values(array)[2];
            $("#messagerDimmer").fadeIn(90);

            messagerDimmer.addEventListener("click", function() {
                self.location = "http://localhost/chat%20proto";
            })
        } else if (array.Status == "Ok") {
            let lastEntranceID = array.Problem[0];
            let sessionEndTime = array.Problem[1]*1000;
            console.log(sessionEndTime)
    
            let currentTime = new Date().valueOf();
            let sessionTimeoutTime = sessionEndTime-currentTime;
            console.log(sessionTimeoutTime)
            setTimeout(sessionInfoGet, sessionTimeoutTime)
        }
    }
}