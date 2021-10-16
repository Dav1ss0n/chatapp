<?php


if (isset($_POST)) {
    $s = new sessioner();
    $s->sessionChecker();
}

class sessioner {
    private $uuid;
    private $conn;
    
    final public function sessionChecker() {
        require("includes/databases/chat.php");
        require("includes/functions/messager-array.php");

        if (!isset($_COOKIE["uuid"])) {
            die(messagerArray_l3("Session", "Not found", "Cookie was not found"));
        } else {
            $this->uuid = hex2bin($_COOKIE["uuid"]);
    
            $chat = new db_connect();
            $conn = $chat->connect();
            $this->conn = $conn;
    
            $prepared = $this->conn->prepare("SELECT ID, Timestamp FROM actions WHERE User = ? ORDER BY ID DESC LIMIT 1");
            if (!$prepared) {
                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
            }
            $prepared->bind_param("s", $this->uuid);
            $prepared->execute();
            $res = $prepared->get_result();
            while ($row = $res->fetch_assoc()) {
                $lastEntranceTime = $row['Timestamp'];
                $lastEntranceID = $row["ID"];
            }
    
            if (strtotime($lastEntranceTime)+31536000 < time()) {
                setcookie("uuid", "", time()-3600, "/");
                echo messagerArray_l3("Session", "Expired", "User session expired");
            } else {
                echo messagerArray_l3("Session", "Ok", [$lastEntranceID, strtotime($lastEntranceTime)+31536000]);
            }
        }
    }
}

?>