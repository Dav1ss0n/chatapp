<?php 

if (isset($_COOKIE["uuid"])) {
    if (isset($_POST["parameter"])) {
        if ($_POST["parameter"] == "bio") {
            $userChange = new userChange();
            $bioChange = $userChange->bio_change();
        } elseif ($_POST["parameter"] == "avi") {
            $userChange = new userChange();
            $aviChange = $userChange->avi_change();
        } elseif ($_POST["parameter"] == "avi_clear") {
            $userChange = new userChange();
            $aviChange = $userChange->avi_change();
        } elseif ($_POST["parameter"] == "username") {
            $userChange = new userChange();
            $usernameChange = $userChange->username_change();
        }
    }
}

class userChange {
    private $parameter;
    private $change;
    private $conn;

    final public function bio_change() {
        require("./includes/databases/chat.php");
        $chat = new db_connect();
        $this->conn = $chat->connect();

        $this->parameter=$_POST["parameter"];
        $this->change=$_POST["change"];

        $currentTime = date("Y-m-d H:i:s");
        $insert_bio = $this->conn->prepare("INSERT INTO bio (User, Bio, DateCreation) VALUES (?, ?, ?)");
        if (!$insert_bio) {
            die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
        }
        $insert_bio->bind_param("sss", $uuid, $this->change, $currentTime);
        $uuid = hex2bin($_COOKIE["uuid"]);
        $insert_bio->execute();
    }

    final public function avi_change() {
        $this->parameter=$_POST["parameter"];
        if ($this->parameter == "avi_clear") {
            require("./includes/databases/chat.php");
            $chat = new db_connect();
            $this->conn = $chat->connect();

            $insert_avi = $this->conn->prepare("INSERT INTO avis (User, Filename, Timestamp) VALUES (?, ?, ?);");
            if (!$insert_avi) {
                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
            }
            $insert_avi->bind_param("sss", $uuid, $new_img_name, $currentTime);
            $uuid = hex2bin($_COOKIE["uuid"]);
            $new_img_name = "";
            $currentTime = date("Y-m-d H:i:s");
            $insert_avi->execute();
        } else {
            if (isset($_FILES['change'])) {
                $this->change=$_FILES["change"];
    
                $extensions = ["jpeg", "png", "jpg"];
                $file_ext = end(explode(".", $this->change["name"]));
                if (in_array($file_ext, $extensions) === true) {
                    $types = ["image/jpeg", "image/jpg", "image/png"];
                    if (in_array($this->change['type'], $types) === true) {
                        require("./includes/databases/chat.php");
                        $chat = new db_connect();
                        $this->conn = $chat->connect();
    
                        $select_folder = $this->conn->prepare("SELECT Path FROM folders WHERE User = ?");
                        if (!$select_folder) {
                            die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                        }
                        $select_folder->bind_param("s", $uuid);
                        $uuid=hex2bin($_COOKIE['uuid']);
                        $select_folder->execute();
                        $row = $select_folder->get_result()->fetch_assoc();
                        $path = $row["Path"];
    
                        $time = time();
                        $new_img_name = $time.$this->change["name"];
                        if (move_uploaded_file($this->change['tmp_name'], "./".$path."photos/avis/".$new_img_name)) {    
                            $currentTime = date("Y-m-d H:i:s");
                            $insert_avi = $this->conn->prepare("INSERT INTO avis (User, Filename, Timestamp) VALUES (?, ?, ?);");
                            if (!$insert_avi) {
                                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                            }
                            $insert_avi->bind_param("sss", $uuid, $new_img_name, $currentTime);
                            $insert_avi->execute();
                        }
    
    
                    }
                }
            }
        }
    }

    final public function username_change() {
        $this->change = json_decode($_POST["change"]);
        require("./includes/functions/messager-array.php");
        require("./includes/databases/chat.php");
        $chat = new db_connect();
        $this->conn = $chat->connect();
        for ($i=0; $i<count($this->change); $i++) {
            if (substr($this->change[$i], 0, 1) == 0) {
                $update_firstname = $this->conn->prepare("UPDATE usernames SET first_name = ? WHERE uuid = ?");
                if (!$update_firstname) {
                    die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                }
                $update_firstname->bind_param("ss", $firstname, $uuid);
                $firstname = substr($this->change[$i], 1, strlen($this->change[$i]));
                $uuid=hex2bin($_COOKIE["uuid"]);
                $update_firstname->execute();
            } elseif (substr($this->change[$i], 0, 1) == 1) {
                $update_lastname = $this->conn->prepare("UPDATE usernames SET last_name = ? WHERE uuid = ?");
                if (!$update_lastname) {
                    die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                }
                $update_lastname->bind_param("ss", $lastname, $uuid);
                $lastname = substr($this->change[$i], 1, strlen($this->change[$i]));
                $uuid=hex2bin($_COOKIE["uuid"]);
                $update_lastname->execute();
            } elseif (substr($this->change[$i], 0, 1) == 2) {
                $select_username = $this->conn->prepare("SELECT Login FROM users WHERE Login = ?");
                if (!$select_username) {
                    die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                }
                $select_username->bind_param('s', $username);
                $username = substr($this->change[$i], 1, strlen($this->change[$i]));
                $select_username->execute();
                if ($select_username->get_result()->num_rows == 1) {
                    die(messagerArray_l3("username_changing", "Not found", "Username(Login) was already taken"));
                } else {
                    $update_username = $this->conn->prepare("UPDATE users SET Login = ? WHERE UUID = ?");
                    if (!$update_username) {
                        die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                    }
                    $update_username->bind_param("ss", $username, $uuid);
                    $username = substr($this->change[$i], 1, strlen($this->change[$i]));
                    $uuid=hex2bin($_COOKIE["uuid"]);
                    $update_username->execute();
                }
            } 
        }
    }




}
?>