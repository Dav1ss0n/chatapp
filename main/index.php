<?php if (!isset($_COOKIE["uuid"])) {header("location: http://localhost/chat proto/login/");} ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="lib/style.css">
    <script defer src="lib/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Main page of kolhozmates</title>
</head>
<body>
    <div id="main">
        <h1>Welcome to Kolhozmates!</h1>
        <h3>This is the place where you can chat!</h3>
    </div>
</body>
</html>