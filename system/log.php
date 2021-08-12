<?php 
ini_set('display_errors',1);
error_reporting(E_ALL);

if (isset($_POST["param"])) {
    if ($_POST["param"] == "login") {
        $user = new logger();
        $user->login(json_decode($_POST['info']));
    } elseif ($_POST["param"] == "register" and isset($_POST["info"])) {
        // echo "1 step";
        $newUser = new logger();
        $newUser->register(json_decode($_POST["info"]));
    } elseif ($_POST["param"] == "sessionChecker") {
        $s = new logger();
        $s->sessionChecker($_POST["entranceTime"], $_POST["uuid"]);
        echo $s;
    }
}


class logger {
    private $login;
    private $email;
    private $password;

    private $conn;

    final public function register(array $info) {
        $this->login = trim($info[0]);
        $this->email = trim($info[1]);
        $this->password = trim($info[2]);

        require("crypto/encrypt/basic_v1.php");
        require("databases/chat.php");
        require("functions/messager-array.php");
        require("functions/uuid-gnrtr.php");
        
        $chat = new db_connect();
        $conn = $chat->connect();
        $this->conn = $conn;
        
        $dataChecker = new dataChecker();
        $loginStatus = $dataChecker->loginChecker($this->login);
        $emailStatus = $dataChecker->emailChecker($this->email, $conn);

        if ($loginStatus) {
            $conn->close();
            echo messagerArray_l3("Register", "Not found", "Login was already taken");
        } elseif ($emailStatus) {
            $conn->close();
            echo messagerArray_l3("Register", "Not found", "Email was already taken");
        } elseif ($loginStatus and $emailStatus) {
            $conn->close();
            echo messagerArray_l3("Register", "Not found", "Login and Email were already taken");
        } else {
            $prepared = $this->conn->prepare("INSERT INTO users (Login, Email, Password, UUID) VALUES (?, ?, ?, ?);");
            $prepared->bind_param("ssss", $de_login, $this->email, $de_password, $uuid);
            $hex = bin2hex(openssl_random_pseudo_bytes(32));
            $de_login = password_hash($this->login, PASSWORD_BCRYPT);
            $de_password = encryptString($this->password, $hex, "base64");
            // echo $de_login."<br/>".$de_password."<br/>";
            $uuid =  gen_uuid();
            $prepared->execute();
            $prepared->close();
            
            $folderName = bin2hex(openssl_random_pseudo_bytes(16));
            $folder_path = "users/$folderName/";
            if (mkdir($folder_path, 0777, true)) {
                if (mkdir($folder_path."photos/", 0777, true) and mkdir($folder_path."videos/", 0777, true)) {
                    $prepared = $this->conn->prepare("INSERT INTO folders (User, Path) VALUES (?, ?);");
                    $prepared->bind_param("ss", $de_login, $folder_path);
                    $prepared->execute();
                    $prepared->close();
                    
                    $file = fopen("users/$folderName/hex.txt", "w");
                    if (fwrite($file, $hex)) {
                        fclose($file);
                        echo messagerArray_l3("Register", "Success", "You are now logged in");
                        $currentTime = date("Y-m-d H:i:s");
                        $actionQuery = $this->conn->prepare("INSERT INTO actions (User, Timestamp) VALUES (?, ?);");
                        if (!$actionQuery) {
                            die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                        }
                        $actionQuery->bind_param("ss", $uuid, $currentTime);
                        $actionQuery->execute();
                        $conn->close();
                        setcookie("uuid", $uuid, time() + 21600, "/");
                    } else {
                        $conn->close();
                        die(messagerArray_l3("File open", "Not found", "Occured unexpected problem with files"));
                    }
                } else {
                    $conn->close();
                    die(messagerArray_l3("Register", "Not found", "Occured unexpected error with data"));
                }
            } else {
                $conn->close();
                die(messagerArray_l3("Register", "Not found", "Occured unexpected error with data"));
            }
        }
    }
    
    final public function login(array $info) {
        $this->login = trim($info[0]);
        $this->password = trim($info[1]);

        require("crypto/decrypt/basic_v1.php");
        require("databases/chat.php");
        require("functions/messager-array.php");
        require("functions/uuid-gnrtr.php");

        $chat = new db_connect();
        $conn = $chat->connect();
        $this->conn = $conn;
        
        if (strstr($this->login, "@") and strstr($this->login, ".")) {
                $prepared = $this->conn->prepare("SELECT * FROM users WHERE Email = ?");
                $prepared->bind_param("s", $this->login);
                $prepared->execute();
                $prepared = $prepared->get_result();
                if ($prepared) {
                    if ($prepared->num_rows == 0) {
                        die(messagerArray_l3("Login", "Not found", "No such an email"));
                    } else {
                        while ($row = $prepared->fetch_assoc()) {
                            $dbLogin = $row["Login"];
                            $dbPass = $row["Password"];
                        }
                        $prepared->close();

                        $query = $this->conn->query("SELECT * FROM folders WHERE User = '$dbLogin'");
                        while ($row = $query->fetch_assoc()) {
                            $path = $row["Path"];
                        }

                        $userHex = file_get_contents($path."hex.txt");

                        $dePassword = decryptString($dbPass, $userHex, "base64");
                        if ($dePassword == $this->password) {
                            die(messagerArray_l3("Login", "Success", "You are now logged in"));
                            $actionQuery = $this->conn->prepare("INSERT INTO actions (User, Timestamp) VALUES (?, ?);");
                            if (!$actionQuery) {
                                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                            }
                            $actionQuery->bind_param("ss", $uuid, $currentTime);
                            $actionQuery->execute();
                            $conn->close();
                            setcookie("uuid", $uuid, time() + 21600, "/");
                        } else {
                            die(messagerArray_l3("Login", "Not found", "Incorrect Password"));
                        }
                    }
                } else {
                    die(messagerArray_l3("Login", "Not found", "Occured unexpected error with data"));
                }
        } else {
            $prepared = $this->conn->prepare("SELECT * FROM users");
            if(!$prepared){ //если ошибка - убиваем процесс и выводим сообщение об ошибке.
                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
            }
            $prepared->execute();
            $prepared = $prepared->get_result();
            if ($prepared) {
                $dbLoginArray = array();
                $dbPassArray = array();
                while ($row = $prepared->fetch_assoc()) {
                    $dbLoginArray[] = $row["Login"];
                    $dbPassArray[] = $row["Password"];
                }
    
                $prepared->close();
                $loginStatusArray = array();
                for ($i=0; $i<count($dbLoginArray); $i++) {
                    if (password_verify($this->login, $dbLoginArray[$i])) {
                        $query = $this->conn->prepare("SELECT * FROM folders WHERE User = ?");
                        if(!$query){ //если ошибка - убиваем процесс и выводим сообщение об ошибке.
                            die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                        }
                        $query->bind_param("s", $dbLoginArray[$i]);
                        $query->execute();
                        $query = $query->get_result();
                        while ($row = $query->fetch_assoc()) {
                            $folder_path = $row["Path"];
                        }
                        
                        $query->close();
                        $userHex = file_get_contents($folder_path."hex.txt");
                        array_push($loginStatusArray, $dbPassArray[$i]);
                    } else {
                        die(messagerArray_l3("Login", "Not found", "No such a login"));
                    }
                }
                if (count($loginStatusArray) == 1) {
                    $suffPassword = $loginStatusArray[0];
                    $dePassword = decryptString($suffPassword, $userHex, "base64");
                    if ($dePassword == $this->password) {
                        die(messagerArray_l3("Login", "Success", "You are now logged in"));
                        $actionQuery = $this->conn->prepare("INSERT INTO actions (User, Timestamp) VALUES (?, ?);");
                        if (!$actionQuery) {
                            die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                        }
                        $actionQuery->bind_param("ss", $uuid, $currentTime);
                        $actionQuery->execute();
                        $conn->close();
                        setcookie("uuid", $uuid, time() + 21600, "/");
                    } else {
                        die(messagerArray_l3("Login", "Not found", "Incorrect Password"));
                    }
                } else {
                    die(messagerArray_l3("Login", "Not found", "No such a login"));
                }
            } else {
                die(messagerArray_l3("Login", "Not found", "Occured unexpected error with data"));
            }
        }

    }

    final public function sessionChecker($value, $user) {
        require("/xampp/htdocs/chat proto/system/functions/sessions/session-time.php");
        
        $sessionChecker = new sessionChecker($value, $user);
        return $sessionChecker;
    }
}

class dataChecker {
    private $conn;

    final public function loginChecker($loginToCheck) {

        $chat = new db_connect();
        $conn = $chat->connect();

        $this->conn = $conn;
        $query = $this->conn->query( "SELECT * FROM users");

        $dbLoginArray = array();
        while ($row = $query->fetch_assoc()) {
            $dbLoginArray[] = $row["Login"];
        }
        
        $sufficientLogin = array();
        for ($i=0; $i<count($dbLoginArray); $i++) {
            if (password_verify($loginToCheck, $dbLoginArray[$i])) {
                array_push($sufficientLogin, $i);
            }
        }
        if (count($sufficientLogin) == 1) {
            return true;
        } else {
            return false;
        }
    }

    final public function emailChecker($emailToCheck, $connection) {
        $prepared = mysqli_prepare($connection, "SELECT * FROM users WHERE Email = ?;");
        $prepared->bind_param("s", $emailToCheck);
        $prepared->execute();
        $result = $prepared->get_result();

        if ($result->num_rows == 1) {
            return true;
        } else {
            return false;
        }
    }
}

?>
