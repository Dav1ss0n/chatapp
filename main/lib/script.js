const message = document.getElementById("message");
const messagerDimmer = document.getElementById("messagerDimmer");
const bioEditButton = document.getElementById("bio-edit-button");
const settingsEnterButton = document.getElementById("settings-enter-button");
const x = document.querySelector(".x");

document.addEventListener("DOMContentLoaded", function() {
    userStatusChanger("Online");
    sessionInfoGet();
    userInfoGet();

    bioEditButton.addEventListener("click", () => {
        $("#user-info-changer-dimmer").fadeIn(90);
    });
    settingsEnterButton.addEventListener("click", () => {
        $("#user-settings-changer-dimmer").fadeIn(90);
    });

    $("#user-info-changer-dimmer").click(() => {
        if (event.target.id == "user-info-changer-dimmer" || event.target.id == "user-info-changer-closer") {
            $("#user-info-changer-dimmer").fadeOut(90);
        }
    })
    $("#user-settings-changer-dimmer").click(() => {
        if (event.target.id == "user-settings-changer-dimmer" || event.target.id == "user-settings-changer-closer") {
            $("#user-settings-changer-dimmer").fadeOut(90);
        }
    })
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

function userInfoGet() {
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            accInfoParse(this.response);
        }
    }
    xml.open("POST", "/chat proto/system/user-info-load.php", true);
    xml.responseType = "json";
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send();
}


function AvatarLetters(userName) {
    let userNames = document.querySelectorAll(".username");
    let avatars = document.querySelectorAll(".user-avatar img");
    
    for (let i = 0; i<userNames.length; i++) {
      let singleUserName = userNames[i].innerText;
      console.log(singleUserName);
      let firstLetter = singleUserName.slice(0, 1);
      singleUserName = singleUserName.replace(/[aeiou]/ig,'');
      
      singleUserName = singleUserName.slice(1, 2);
      singleUserName = firstLetter + singleUserName;
      avatars[i].alt = singleUserName;
    }

    // let avatar = document.getElementById("user-avatar-img");

    // let singleUserName = userName;
    // let firstLetter = singleUserName.slice(0, 1);
    // singleUserName = singleUserName.replace(/[aeiou]/ig,'');
    
    // singleUserName = singleUserName.slice(1, 2);
    // let alt = firstLetter + singleUserName;
    // console.log(alt);

    // avatar.setAttribute('alt', alt);
  }

  function accInfoParse(array) {
      document.getElementById("username").innerText = array.username;
      if (!array.avi) {
          AvatarLetters(array.username);
      }
  }