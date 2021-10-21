<?php 
if (isset($_COOKIE["uuid"])) {
    if (isset($_POST)) {
        $user = new accInfo();
    }
} else {
    require("../system/includes/functions/messager-array.php");
    die(messagerArray_l3("Account Info Load", "Denied", "Cookie was not found"));
}

class accInfo {
    private $conn;
    private $uuid;

    final public function __construct() {
        require("../system/includes/databases/chat.php");
        require("../system/includes/functions/messager-array.php");

        $chat = new db_connect();
        $this->conn = $chat->connect();

        $uuid_checker = $this->conn->prepare("SELECT Login FROM users WHERE UUID = ?");
        if (!$uuid_checker) {
            die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
        }
        $uuid_checker->bind_param("s", $givenUuid);
        $givenUuid = hex2bin($_COOKIE["uuid"]);
        $uuid_checker->execute();
        $result = $uuid_checker->get_result();

        if ($result->num_rows == 0) {
            die(messagerArray_l3("Account Info Load", "Denied", "Unappropriate uuid"));
        } else {
            // taking username
            $this->uuid = hex2bin($_COOKIE["uuid"]);
            $row = $result->fetch_assoc();
            $user_name = $row["Login"];
            
            // taking user folder path
            $select_path = $this->conn->prepare("SELECT Path FROM folders WHERE User = ?");
            if (!$select_path) {
                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
            }
            $select_path->bind_param("s", $this->uuid);
            $select_path->execute();
            $row = $select_path->get_result()->fetch_assoc();
            $user_path = $row["Path"];

            // taking user's last avi
            $select_lastAvi = $this->conn->prepare("SELECT Filename FROM avis WHERE User = ? ORDER BY ID DESC LIMIT 1");
            if (!$select_lastAvi) {
                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
            }
            $select_lastAvi->bind_param("s", $this->uuid);
            $select_lastAvi->execute();
            $res = $select_lastAvi->get_result();
            if ($res->num_rows == 0) {
                $user_avi = "";
            } else {
                $row = $res->fetch_assoc();
                $user_avi = $row["Filename"];
            }

            // taking user's bio
            $select_bio = $this->conn->prepare("SELECT Bio FROM bio WHERE User = ? ORDER BY ID DESC LIMIT 1");
            if (!$select_bio) {
                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
            }
            $select_bio->bind_param("s", $this->uuid);
            $select_bio->execute();
            $result = $select_bio->get_result();
            $row = $result->fetch_assoc();
            if (empty($row["Bio"])) {
                $user_bio = "";
            } else {
                $user_bio = $row["Bio"];
            }

            //taking user's names
            $select_names = $this->conn->prepare("SELECT first_name, last_name FROM usernames WHERE uuid = ?");
            if (!$select_names) {
                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
            }
            $select_names->bind_param("s", $this->uuid);
            $select_names->execute();
            $res = $select_names->get_result();
            while ($row = $res->fetch_assoc()) {
                $firstname = $row["first_name"];
                $lastname = $row["last_name"];
            }

            $user_info = array(
                "username" => $user_name,
                "firstname" => $firstname,
                "lastname" => $lastname,
                "uuid" => $_COOKIE["uuid"],
                "bio" => $user_bio,
                
                "folder_name" => $user_path,
                "avi" => $user_avi
            );
            die(json_encode($user_info));
        }
        
        

    }

}
?>