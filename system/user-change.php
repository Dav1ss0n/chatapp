<?php 

if (isset($_COOKIE["uuid"])) {
    if (isset($_POST["parameter"]) and isset($_POST["change"])) {
        $userChange = new userChange;
    }
}

class userChange {
    private $parameter;
    private $change;
    private $conn;

    final public function __construct() {
        require("./includes/databases/chat.php");
        $chat = new db_connect();
        $this->conn = $chat->connect();

        $this->parameter=$_POST["parameter"];
        $this->change=$_POST["change"];

        $currentTime = date("Y-m-d H:i:s");
        if ($this->parameter == "bio") {
            $insert_bio = $this->conn->prepare("INSERT INTO bio (User, Bio, DateCreation) VALUES (?, ?, ?)");
            if (!$insert_bio) {
                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
            }
            $insert_bio->bind_param("sss", $uuid, $this->change, $currentTime);
            $uuid = hex2bin($_COOKIE["uuid"]);
            $insert_bio->execute();
        }
    }
}
?>