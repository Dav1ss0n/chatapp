const message = document.getElementById("message");
const messagerDimmer = document.getElementById("messagerDimmer");
const bioEditButton = document.getElementById("bio-edit-button");
const settingsEnterButton = document.getElementById("settings-enter-button");
const signOutButton = document.getElementById("signOut-button");
const x = document.querySelector(".x");
let userBio;


document.addEventListener("DOMContentLoaded", function() {
    // $("#confirmerDimmer").fadeIn(90);
    
    // setTimeout($('.slide-down-messager').slideUp(300), 1000000);

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

    signOutButton.addEventListener("click", () => {
        $("#confirmerDimmer").fadeIn(90);
    })
    $("#confirmerCancel").click(() => {
        $("#confirmerDimmer").fadeOut(90);
    });
    $("#confirmerApply").click(() => {
        userStatusChanger("Offline");
        // self.location = "http://localhost/chat proto/";
    });
    $("#confirmerDimmer").click(() => {
        if (event.target.id == "confirmerDimmer") {
            $("#confirmerDimmer").fadeOut(90);
        }
    })

    document.getElementById("user-bio-clear").addEventListener("click", () => {
        document.getElementById("user-bio").value = "";
    })
    document.getElementById("user-bio").addEventListener("keyup", () => {
        document.getElementById("user-bio").value
        
        let userBioRemainingSymbols = 100 - document.getElementById("user-bio").value.length;
        document.getElementById("user-bio-remaining-symbols").innerText = userBioRemainingSymbols+" symbols left";
        if (userBioRemainingSymbols == 0 || userBioRemainingSymbols < 0) {
            document.getElementById("user-bio").value = document.getElementById("user-bio").value.substr(0, 100);
            document.getElementById("user-bio-remaining-symbols").innerText = "0 symbols left";
        }
        if (document.getElementById("user-bio").value !== userBio) {
            document.getElementById("user-bio-save").disabled=false;
        }
    })
    document.getElementById("user-bio-save").addEventListener("click", () => {
        document.getElementById("user-bio-save").disabled=true;
        // console.log(document.getElementById("user-bio").value);
        userChange("bio", document.getElementById("user-bio").value);
        $('.slide-down-messager').slideDown(300);
        $(".x-slide-down").click(() => {
            $('.slide-down-messager').slideUp(300);
        })
        setTimeout(() => {
            $('.slide-down-messager').slideUp(300);
        }, 10000)
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
    } else if (status == "Offline") {
        self.location = "http://localhost/chat proto";
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
            // console.log(sessionEndTime)
    
            let currentTime = new Date().valueOf();
            let sessionTimeoutTime = sessionEndTime-currentTime;
            // console.log(sessionTimeoutTime)
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
    //   console.log(singleUserName);
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
      document.getElementById("username").innerText = array.firstname+" "+array.lastname;

      document.getElementById("user-bio").value = array.bio;
      document.getElementById("user-bio-remaining-symbols").innerText = 100 - array.bio.length+" symbols left";
      userBio = array.bio;
      if (!array.avi) {
          AvatarLetters(array.username);
      }

  }


function userChange(parameter, change) {
    let xml = new XMLHttpRequest();
    // xml.onreadystatechange = () => {
    //     if (this.readyState == 4 && this.status == 200) {
    //         if (this.responseText !== "") {
    //             console.log(this.responseText);
    //         } else {

    //         }
    //     }
    // }
    xml.open("POST", "/chat proto/system/user-change.php", true);
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send("parameter="+parameter+"&change="+change);
}