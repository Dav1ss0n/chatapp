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

document.addEventListener("DOMContentLoaded", function() {
    enterBtn.addEventListener("click", (e) => {
        e.preventDefault();
        let errors = [];
        if (login.value === '' || password.value == null) {
            errors.push('Login is required')
        }
        if (password.value === '' || password.value == null || password.value.length < 6) {
            errors.push("Password is longer than 6")
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

            xml.open("POST", "/chat proto/system/log.php", true);
            xml.responseType = "json";
            xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xml.send("param=login&info="+JSON.stringify(info));
        }
    });
    // Register box set up
    registerA.addEventListener("click", function() {
        $("#registerBoxDimmer").fadeIn(90);
    });
    regBoxCloser.addEventListener("click", function() {
        $("#registerBoxDimmer").fadeOut(90);
    })
    $("#registerBoxDimmer").click(function() {
        console.log(event.target.id);
        if (event.target.id == "registerBoxDimmer") {
            $("#registerBoxDimmer").fadeOut(90);
          }
    })

    //Register button set up
    registerBtn.addEventListener("click", function(e) {
        let registerErrors = [];
        if (regLogin === '' || regLogin == null || regLogin.value.length < 6) {
            registerErrors.push("Login is longer than 6");
        }
        if (regEmail === '' || regEmail == null || regEmail.value.includes("@") === false) {
            registerErrors.push("Incorrect Email format");
        }
        if (regPassC1 === '' || regPassC1 == null || regPassC1.value.length < 6) {
            registerErrors.push("Password is longer than 6");
        }
        if (regPassC1.value.length !== regPassC2.value.length) {
            registerErrors.push("Password mismatch");
        }

        if (registerErrors.length > 0) {
            e.preventDefault();
            regErrorsBox.style = "display: block"
            regErrorsBox.innerText = registerErrors.join("\n");
        }
        else {
            let info = [regLogin.value, regEmail.value, regPassC1.value];
            regErrorsBox.style = "display: none"

            let xml = new XMLHttpRequest();
            xml.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    statusChecker(this.response);
                }
            }

            xml.open("POST", "/chat proto/system/log.php", true);
            xml.responseType = "json";
            xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xml.send("param=register&info="+JSON.stringify(info));
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
            message.innerText = array.Problem;
            $("#messagerDimmer").fadeIn(90);

            messagerDimmer.addEventListener("click", function() {
                $("#messagerDimmer").fadeOut(90);
            })
        }
    }
}