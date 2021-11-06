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
        } elseif ($_POST["parameter"] == "accDelete") {
            $userChange = new userChange();
            $accDelete = $userChange->accDelete();
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

    final public function accDelete() {
        require("./includes/functions/messager-array.php");
        require("./includes/databases/chat.php");
        $chat = new db_connect();
        $this->conn = $chat->connect();

        $check_uuid = $this->conn->prepare("SELECT Path FROM folders WHERE User = ? LIMIT 1");
        if (!$check_uuid) {
            die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
        }
        $check_uuid->bind_param("s", $uuid);
        $uuid = hex2bin($_COOKIE["uuid"]);
        $check_uuid->execute();
        $res = $check_uuid->get_result();
        if ($res->num_rows == 0) {
            die(messagerArray_l3("Acc delete", "Denied", "No such as uuid"));
        } else {
            $row = $res->fetch_assoc();
            $path = $row["Path"];

            $delete_usersTable = $this->conn->prepare("DELETE FROM users WHERE UUID = ?");
            if (!$delete_usersTable) {
                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
            }
            $delete_usersTable->bind_param("s", $uuid);
            $uuid = hex2bin($_COOKIE["uuid"]);
            $delete_usersTable->execute();


            if (new removeDir("../system/".$path)) {
                $delete_folder = $this->conn->prepare("DELETE FROM folders WHERE User = ?");
                if (!$delete_folder) {
                    die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                }
                $delete_folder->bind_param("s", $uuid);
                $uuid = hex2bin($_COOKIE["uuid"]);
                $delete_folder->execute();


                $delete_usernames = $this->conn->prepare("DELETE FROM usernames WHERE uuid = ?");
                if (!$delete_usernames) {
                    die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                }
                $delete_usernames->bind_param("s", $uuid);
                $uuid = hex2bin($_COOKIE["uuid"]);
                $delete_usernames->execute();


                $delete_avis = $this->conn->prepare("DELETE FROM avis WHERE User = ?");
                if (!$delete_avis) {
                    die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                }
                $delete_avis->bind_param("s", $uuid);
                $uuid = hex2bin($_COOKIE["uuid"]);
                $delete_avis->execute();


                $delete_bio = $this->conn->prepare("DELETE FROM bio WHERE User = ?");
                if (!$delete_bio) {
                    die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                }
                $delete_bio->bind_param("s", $uuid);
                $uuid = hex2bin($_COOKIE["uuid"]);
                $delete_bio->execute();
    
    
                $update_userStatus = $this->conn->prepare("UPDATE user_statuses SET Status = ? WHERE User = ?");
                if (!$update_userStatus) {
                    die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                }
                $update_userStatus->bind_param("ss", $status, $uuid);
                $status="Deleted";
                $uuid = hex2bin($_COOKIE["uuid"]);
                $update_userStatus->execute();

                if (setcookie("uuid", "", time()-3600, "/")) {
                    die(messagerArray_l3("Acc delete", "Completed", "Deletion was completed succesfully"));
                }
            }

        }
    }





}


class removeDir {
    final public function __construct($dirname) {
        if (is_dir($dirname)) {
            $dir = new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS);
            foreach (new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST) as $object) {
                if ($object->isFile()) {
                    unlink($object);
                } elseif($object->isDir()) {
                    rmdir($object);
                } else {
                    throw new Exception('Unknown object type: '. $object->getFileName());
                }
            }
            rmdir($dirname); // Now remove myfolder
        } else {
            throw new Exception('This is not a directory');
        }
    }

}
?>