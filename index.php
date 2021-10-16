<?php
if (isset($_COOKIE["uuid"])) {
    header("location: http://localhost/chat proto/main/");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="lib/style.css">
    <script defer src="lib/script.js"></script>
    <script src="/chat proto/system//libraries/jquery-3.6.0.min.js"></script>
    <title>Log in kolhozmates</title>
</head>
<body>
    <div id="info">
        <div id="log-in">
            <div id="log-in-content">
                <form method="POST" action="/">
                    <input type="text" id="login" placeholder="Your Login" autocomplete="off">
                    <br/>
                    <input type="password" id="password" placeholder="Your password" autocomplete="off">
                    <br/>
                    <div id="error-messages" class="error-messages"></div>
                    <span id="reg-message">Not a member? <a id="registerOpen">Register</a></span>
                    <input type="button" id="submit" value="Enter">
                </form>
            </div>
        </div>

        <div id="registerBoxDimmer">
            <div id="registerBox">
                <div id="registerBoxContent">
                    <span id="regCloser">+</span>
                    <br/>
                    <input type="text" id="regLogin" placeholder="Your Login">
                    <br/>
                    <div id="names">
                        <input type="text" id="regName1" placeholder="Your First Name">
                        <input type="text" id="regName2" placeholder="Your Last Name">
                    </div>
                    <br/>
                    <input type="text" id="regEmail" placeholder="Your Email">
                    <!-- <button id="emailConfirm">Confirm</button> -->
                    <br/>
                    <input type="password" id="regPassC1" placeholder="Your Password">
                    <br/>
                    <input type="password" id="regPassC2" placeholder="Confirm Pasword">
                    <div id="reg-error-messages" class="error-messages">error</div>
                    <button id="register">Enter</button>
                </div>
            </div>
        </div>

        <div id="messagerDimmer">
            <div id="messager">
                <div id="messagerContent">
                    <span id="message"></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>