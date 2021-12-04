<?php 
if (!isset($_COOKIE["uuid"])) {
    header("location: http://localhost/chat%20proto/");
    } else {
        session_start();
        $_SESSION["uuid"] = $_COOKIE["uuid"];
    }
     
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/chat proto/main/lib/style.css">
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="../system/libraries/css/font-awesome.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"> -->
    <link rel="stylesheet" href="../system/libraries/css/all.min.css">
    <script src="/chat proto/system//libraries/jquery-3.6.0.min.js"></script>
    <title>Main page of kolhozmates</title>
</head>
<body>
    <div id="main">
        <div id="pre-profile">
            <div id="pre-profile-content">
                <div class="user-avatar">
                    <img class="user-avatar-img" id="user-avatar-img" src="#" alt>
                </div>
                <div id="username-and-status">
                  <div class="username" id="username">Dave Davission</div>
                  <div id="user-status-content">
                    <span id="user-status-dot">•</span>
                    <span id="user-status">Online</span>
                  </div>
                </div>
                <svg id="bio-edit-button" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                    <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001z"/>
                </svg>
                <svg id="settings-enter-button" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                    <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                    <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
                </svg>
                <svg id="signOut-button" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                </svg>
            </div>

            <div class="users">
            <div class="search">
                <span class="text">Select an user to start chat</span>
                <input type="text" placeholder="Enter name to search..." control-id="ControlID-1">
                <button control-id="ControlID-2"><i class="fas fa-search"></i></button>
            </div>
            </div>
        </div>            

        <div class="popup-windows-dimmer" id="user-info-changer-dimmer">
            <div class="popup-windows">
                <div class="popup-windows-content">
                    <span class="x" id="user-info-changer-closer">+</span>

                    <label for="user-bio-label" class="user-settings-label">Your bio:</label>
                    <textarea id="user-bio" autocomplete="off"></textarea>
                    <span id="user-bio-remaining-symbols">100 characters left</span> <br/>


                    <button id="user-bio-clear">Clear</button>
                    <button id="user-bio-save" disabled>Save</button>
                </div>
            </div>
        </div>
        <div class="popup-windows-dimmer" id="user-settings-changer-dimmer">
            <div class="popup-windows">
                <div class="popup-windows-content">
                    <span class="x" id="user-settings-changer-closer">+</span>

                    <button id="acc-delete" class="red-button">Delete an account</button>
                </div>
            </div>
        </div>
        <div class="popup-windows-dimmer" id="user-avi-changer-dimmer">
            <div class="popup-windows">
                <div class="popup-windows-content">
                    <span class="x" id="user-avi-changer-closer">+</span>

                    <div class="user-avatar" id="avi-changing-window">
                        <img class="user-avatar-img" id="user-avatar-changing-img" src="#" alt>
                    </div>
                    <div id="img-removing">
                            <div id="img-remover">+</div>
                    </div>
 
                    <div class="example-2">
                    <div class="form-group">
                        <form id="sendForm" action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
                            <input type="file" name="file" id="file" class="input-file" accept="image/x-png,image/gif,image/jpeg,image/jpg" onchange="readURL(this);">
                            <label for="file" class="btn btn-tertiary js-labelFile">
                            <i class="icon fa fa-check"></i>
                            <span class="js-fileName">Load image</span>
                            </label>
                        </form>
                    </div>

                    <input type="button" id="avi-saver" value="Save image" disabled>
                    </div>
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
        <div class="slide-down-messager">
            <div class="slide-down-messager-dimmer-content">
                <span class="x-slide-down">+</span>
                <span class="slide-down-message">Changes were saved</span>
            </div>
        </div>
        <div id="confirmerDimmer" class="popup-windows-dimmer">
            <div id="confirmer" class="popup-windows">
                <div id="confirmerContent" class="popup-windows-content">
                    <span id="confrimerMessage">Are you sure?</span> <br/>
                    <div id="confirmerActions">
                    <button id="confirmerCancel">Cancel</button> <button id="confirmerApply" class="red-button">Exit</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="username-changer-dimmer" class="popup-windows-dimmer">
            <div id="username-changer" class="popup-windows">
                <div id="username-changer-content" class="popup-windows-content">
                    <span class="x" id="username-changer-closer">+</span>

                    <label for="username-shorted" class="user-settings-label">Your full name:</label>
                    <br/>
                    <input type="text" id="username-firstname" value="" autocomplete="off"> 
                    <input type="text" id="username-lastname" value="" autocomplete="off"> 

                    <label for="username-shorted" class="user-settings-label">Your username:</label>
                    <span id="username-shorted-sign">@</span>
                    <input type="text" id="username-shorted" value="" oninput="let p = this.selectionStart; this.value = this.value.toLowerCase();this.setSelectionRange(p, p);" onkeypress ="return suppressNonEng(event)" autocomplete="off"> 

                    <button id="username-save" disabled>Save</button>
                </div>
            </div>
        </div>


    </div>
</body>
    <script defer src="/chat proto/main/lib/script.js"></script>
</html>