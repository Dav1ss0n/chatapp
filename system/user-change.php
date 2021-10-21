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




}
?>