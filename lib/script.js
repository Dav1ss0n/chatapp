const enterBtn = document.getElementById("submit");
const registerA = document.getElementById("registerOpen");
const login = document.getElementById("login");
const password = document.getElementById("password");
const form = document.getElementById("form");
const errorBox = document.getElementById("error-messages");
const regBoxCloser = document.getElementById("regCloser");
const registerBtn = document.getElementById("register");
const regLogin = document.getElementById("regLogin");
const regEmail = document.getElementById("regEmail");
const regPassC1 = document.getElementById("regPassC1");
const regPassC2 = document.getElementById("regPassC2");
const regErrorsBox = document.getElementById("reg-error-messages");
const message = document.getElementById("message");
const messagerDimmer = document.getElementById("messagerDimmer");
const emailConfirmBtn = document.getElementById("emailConfirm");
const regName1 = document.getElementById("regName1");
const regName2 = document.getElementById("regName2");

let lastDiv;

document.addEventListener("DOMContentLoaded", function() {
    enterBtn.addEventListener("click", (e) => {
        e.preventDefault();
        loginParser();
    });
    // Register box set up
    registerA.addEventListener("click", function() {
        $("#registerBoxDimmer").fadeIn(90);
    });
    regBoxCloser.addEventListener("click", function() {
        $("#registerBoxDimmer").fadeOut(90);
    })
    $("#registerBoxDimmer").click(function() {
        // console.log(event.target.id);
        if (event.target.id == "registerBoxDimmer") {
            $("#registerBoxDimmer").fadeOut(90);
          }
    })

    //Register button set up
    registerBtn.addEventListener("click", function(e) {
        let registerErrors = [];
        if (regLogin === '' || regLogin == null || regLogin.value.length < 6) {
            registerErrors.push("Login must be longer than 6");
        }
        if (regEmail === '' || regEmail == null || regEmail.value.includes("@") === false) {
            registerErrors.push("Incorrect Email format");
        }
        if (regPassC1 === '' || regPassC1 == null || regPassC1.value.length < 6) {
            registerErrors.push("Password must be longer than 6");
        }
        if (regPassC1.value.length !== regPassC2.value.length) {
            registerErrors.push("Password mismatch");
        }
        if (regName1.value === '' || regName1.value === null) {
            registerErrors.push("Empty first name");
        }
        if (regName2.value === '' || regName2.value === null) {
            registerErrors.push("Empty last name");
        }

        if (registerErrors.length > 0) {
            e.preventDefault();
            regErrorsBox.style = "display: block"
            regErrorsBox.innerText = registerErrors.join("\n");
        }
        else {
            let info = [regLogin.value, regName1.value, regName2.value, regEmail.value, regPassC1.value];
            regErrorsBox.style = "display: none"

            let xml = new XMLHttpRequest();
            xml.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    statusChecker(this.response);
                }
            }

            xml.open("POST", "/chat%20proto/system/log.php", true);
            xml.responseType = "json";
            xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xml.send("param=register&info="+JSON.stringify(info));
        }
    })

    document.getElementById("regLogin").addEventListener("keyup", ()=>{
        if (document.getElementById("regLogin").value.length > 14) {
            document.getElementById("regLogin").value = document.getElementById("regLogin").value.substr(0, 16);
        }
    })



})

function statusChecker(array) {
    if (Object.keys(array).length == 2) {
        if (array.Status == "Not found") {
            // messagerDimmer.style = "display: block";
            message.innerText = `Incorrect ${array.Method} or Password`;
            $("#messagerDimmer").fadeIn(90);

            messagerDimmer.addEventListener("click", function() {
                $("#messagerDimmer").fadeOut(90);
            })
        }
        else {
            message.innerText = "";
        }
    } else if (Object.keys(array).length == 3) {
        if (array.Status == "Not found") {
            if (array.Parameter == "Register") {
                regErrorsBox.style = "display: block"
                regErrorsBox.innerText = array.Problem;
            } else {
                errorBox.style = "display: block"
                errorBox.innerText = array.Problem;
            }
        } else if (array.Status == "Success") {
            self.location = "http://localhost/chat%20proto/main/";
        } else if (array.Parameter == "Cookie" || array.Status == "Found") {
            self.location = "http://localhost/chat%20proto/main/";
        }
    }
}

function loginParser() {
    let errors = [];
    if (login.value === '' || password.value == null) {
        errors.push('Login is required')
    }
    if (password.value === '' || password.value == null) {
        errors.push("Password is required")
    }

    if (errors.length > 0) {
        errorBox.style = "display: block"
        errorBox.innerText = errors.join("\n");
    }
    else {
        errorBox.style = "display: none"

        let info = [login.value, password.value];
        let xml = new XMLHttpRequest();
        xml.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                statusChecker(this.response);
                // console.log(this.responseText);
            }
        }

        xml.open("POST", "/chat%20proto/system/log.php", true);
        xml.responseType = "json";
        xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xml.send("param=login&info="+JSON.stringify(info));
    }
}





function  suppressNonEng(e) {

  let key;
  if(window.event)  key = window.event.keyCode;     //IE
  else  key = e.which;     //firefox


  if(key >128)  return false;
  else  return true;
}