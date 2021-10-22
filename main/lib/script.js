const message = document.getElementById("message");
const messagerDimmer = document.getElementById("messagerDimmer");
const bioEditButton = document.getElementById("bio-edit-button");
const settingsEnterButton = document.getElementById("settings-enter-button");
const signOutButton = document.getElementById("signOut-button");
const x = document.querySelector(".x");
let userBio;
let userFullName;


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
    $("#user-avi-changer-dimmer").click(() => {
        if (event.target.id == "user-avi-changer-dimmer" || event.target.id == "user-avi-changer-closer") {
            $("#user-avi-changer-dimmer").fadeOut(90);
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
        // self.location = "http://localhost/chat%20proto/";
    });
    $("#confirmerDimmer").click(() => {
        if (event.target.id == "confirmerDimmer") {
            $("#confirmerDimmer").fadeOut(90);
        }
    })

    document.getElementById("user-bio-clear").addEventListener("click", () => {
        document.getElementById("user-bio").value = "";
        document.getElementById("user-bio-save").disabled=false;
    })
    document.getElementById("user-bio").addEventListener("keyup", () => {
        document.getElementById("user-bio").value
        
        let userBioRemainingSymbols = 100 - document.getElementById("user-bio").value.length;
        document.getElementById("user-bio-remaining-symbols").innerText = userBioRemainingSymbols+" characters left";
        if (userBioRemainingSymbols == 0 || userBioRemainingSymbols < 0) {
            document.getElementById("user-bio").value = document.getElementById("user-bio").value.substr(0, 100);
            document.getElementById("user-bio-remaining-symbols").innerText = "0 characters left";
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
        }, 7000)
        $("#user-info-changer-dimmer").fadeOut(90);
    });
    // document.getElementById("user-info-changer-dimmer").addEventListener("keypress", (e) => {
    //     if (e.key==="Enter") {
    //         document.getElementById("user-bio-save").disabled=true;
    //         // console.log(document.getElementById("user-bio").value);
    //         userChange("bio", document.getElementById("user-bio").value);
    //         $('.slide-down-messager').slideDown(300);
    //         $(".x-slide-down").click(() => {
    //             $('.slide-down-messager').slideUp(300);
    //         })
    //         setTimeout(() => {
    //             $('.slide-down-messager').slideUp(300);
    //         }, 10000)
    //     }
    // })

    document.getElementById("user-avatar-img").addEventListener("click", ()=>{
        $("#user-avi-changer-dimmer").fadeIn(90);
    })

    document.getElementById("avi-saver").addEventListener("click", ()=> {
        document.getElementById("avi-saver").disabled = true;
        let photos = document.getElementById("file").files;
        let photo = photos[0];
        // let data=new FormData(document.getElementById("sendForm"));
        // data.append("parameter", "avi");
        // data.append("change", photo, photo.name);
        // console.log(data);

        // let xml = new XMLHttpRequest();
        // xml.open("POST", "/chat%20proto/system/user-change.php", true);
        // xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        // xml.send(photo);

        
        let formdata = new FormData();	
        formdata.append("parameter", "avi");
        formdata.append("change", photo);
        $.ajax({
            url: "/chat%20proto/system/user-change.php",
            type: "POST",
            data: formdata,
            processData: false,
            contentType: false,
            success:function(){
                userInfoGet();
                $('.slide-down-messager').slideDown(300);
                $(".x-slide-down").click(() => {
                    $('.slide-down-messager').slideUp(300);
                })
                setTimeout(() => {
                    $('.slide-down-messager').slideUp(300);
                }, 7000)

                $("#user-avi-changer-dimmer").fadeOut(90);
            }
        });
    })

    document.getElementById("avi-changing-window").addEventListener("mouseover", ()=>{
        if (event.target.id == "user-avatar-changing-img" || event.target.id == "img-remover") {
            document.getElementById("img-removing").style = "display: block;"
        } else {
            document.getElementById("img-removing").style = "display: none;"
        }
    });
    document.getElementById("img-remover").addEventListener("click", ()=>{
        let avis = document.querySelectorAll(".user-avatar-img");
        for (let i=0; i<avis.length; i++) {
            avis[i].src = "#";
        }

        userChange("avi_clear", "");

        document.querySelector(".slide-down-message").innerText = "Photo was removed";
        document.querySelector(".slide-down-messager").classList.add("slide-down-messager-red");
        $('.slide-down-messager').slideDown(300);
        $(".x-slide-down").click(() => {
            $('.slide-down-messager').slideUp(300);
        })
        setTimeout(() => {
            $('.slide-down-messager').slideUp(300);
            document.querySelector(".slide-down-messager").classList.remove("slide-down-messager-red");
        }, 7000)
        $("#user-avi-changer-dimmer").fadeOut(90);
    })

    // document.getElementById("username-full").addEventListener("keyup", ()=> {
    //     if (document.getElementById("username-full").value !== userFullName) {
    //         if (document.getElementById("username-full").value == "" || document.getElementById("username-full").value == null) {
    //         document.getElementById("user-bio-save").disabled=true;
    //         } else {
    //             document.getElementById("user-bio-save").disabled=false;
    //         }
    //     }
    // })

    document.querySelector("#pre-profile-content").addEventListener("mouseover", ()=>{
        if (event.target.id == "username") {
            document.getElementById("username").classList.add("username-underline");
        } else {
            document.getElementById("username").classList.remove("username-underline");
        }
    })



});

// window.onbeforeunload = null;
// window.onbeforeunload = function(e) {
//     e.preventDefault();
//     userStatusChanger("Offline");
//     return null;
// }

function userStatusChanger(status) {
    let xml = new XMLHttpRequest();
    xml.open("POST", "/chat%20proto/system/statuser.php", true);
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send("status="+status);
    document.getElementById("user-status").innerText = status;
    if (status == "Online") {
        const userStatusDot = document.getElementById("user-status-dot");
        userStatusDot.style = "color: #00bb16;"
    } else if (status == "Offline") {
        setTimeout(()=>{
            self.location = "http://localhost/chat%20proto";
        }, 350)
    }
}
 
function sessionInfoGet() {
    let xml = new XMLHttpRequest();
    xml.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            sessionChecker(this.response);
        }
    }
    xml.open("POST", "/chat%20proto/system/session-time.php", true);
    xml.responseType = "json";
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send();
}

function sessionChecker(array) {
    if (array.Parameter == "Session") {
        if (array.Status == "Not found") {
            // self.location = "http://localhost/chat%20proto";
            message.innerText = Object.values(array)[2];
            $("#messagerDimmer").fadeIn(90);
            setTimeout(()=> {
                self.location = "http://localhost/chat%20proto";
            }, 10000);
            messagerDimmer.addEventListener("click", function() {
                self.location = "http://localhost/chat%20proto";
            })
        } else if (array.Status == "Ok") {
            let lastEntranceID = array.Problem[0];
            let sessionEndTime = array.Problem[1]*1000;
            // console.log(sessionEndTime)
    
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
    xml.open("POST", "/chat%20proto/system/user-info-load.php", true);
    xml.responseType = "json";
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send();
}


function AvatarLetters(userName) {
    let userNames = document.querySelector(".username");
    let avatars = document.querySelectorAll(".user-avatar-img");
    
    for (let i = 0; i<avatars.length; i++) {
      let singleUserName = userNames.innerText;
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
    //   document.getElementById("username-full").value = array.firstname+" "+array.lastname;
    //   document.getElementById("username-shorted").value = array.username;

      document.getElementById("user-bio").value = array.bio;
      document.getElementById("user-bio-remaining-symbols").innerText = 100 - array.bio.length+" characters left";
      userBio = array.bio;
      userFullName = array.firstname+" "+array.lastname;
      if (!array.avi) {
          AvatarLetters(array.username);
      } else {
          let avis = document.querySelectorAll(".user-avatar-img");
          for (let i=0; i<avis.length; i++) {
              avis[i].src = "/chat%20proto/system/"+array.folder_name+"photos/avis/"+array.avi;
          }
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
    xml.onreadystatechange = function() {
        if (this.readyState==4 && this.status==200) {
            userInfoGet();
        }
    }
    xml.open("POST", "/chat%20proto/system/user-change.php", true);
    xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xml.send("parameter="+parameter+"&change="+change);
}



(function() {
   
    'use strict';
   
    $('.input-file').each(function() {
      var $input = $(this),
          $label = $input.next('.js-labelFile'),
          labelVal = $label.html();
       
     $input.on('change', function(element) {
        var fileName = '';
        if (element.target.value) fileName = element.target.value.split('\\').pop();
        fileName ? $label.addClass('has-file').find('.js-fileName').html(fileName) : $label.removeClass('has-file').html(labelVal);
     });
    });
   
  })();


function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#user-avatar-changing-img')
                .attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);

        document.getElementById("avi-saver").disabled = false;
    }
}
