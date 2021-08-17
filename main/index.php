<?php 
if (!isset($_COOKIE["uuid"])) {
    header("location: http://localhost/chat proto/login/");
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
    <link rel="stylesheet" href="lib/style.css">
    <script defer src="lib/script.js"></script>
    <script src="/system/libraries/jquery-3.6.0.min.js"></script>
    <title>Main page of kolhozmates</title>
</head>
<body>
    <div id="main">
        <?php echo hex2bin($_SESSION["uuid"])."<br/>"; echo ($_SESSION["uuid"]); ?>
    </div>
</body>
</html>