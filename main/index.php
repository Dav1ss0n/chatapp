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
    <script defer src="/chat proto/main/lib/script.js"></script>
    <script src="/chat proto/system//libraries/jquery-3.6.0.min.js"></script>
    <title>Main page of kolhozmates</title>
</head>
<body>
    <div id="main">
        <?php echo hex2bin($_SESSION["uuid"])."<br/>"; echo ($_SESSION["uuid"]); ?>
    </div>

    <div id="messagerDimmer">
            <div id="messager">
                <div id="messagerContent">
                    <span id="message"></span>
                </div>
            </div>
        </div>
</body>
</html>