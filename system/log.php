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
            $prepared->bind_param("ssss", $this->login, $this->email, $de_password, $uuid);
            $de_password = password_hash($this->password, PASSWORD_BCRYPT);
            $uuid =  gen_uuid();
            $prepared->execute();
            $prepared->close();
            
            $folderName = bin2hex(openssl_random_pseudo_bytes(16));
            $folder_path = "users/$folderName/";
            if (mkdir($folder_path, 0777, true)) {
                if (mkdir($folder_path."photos/", 0777, true) and mkdir($folder_path."videos/", 0777, true)) {
                    // creating package.json with user data
                    $package_json = fopen($folder_path."package.json", "a");
                    if ($package_json) {
                        fclose($package_json);

                        // inserting user folder's path into db
                        $prepared = $this->conn->prepare("INSERT INTO folders (User, Path) VALUES (?, ?);");
                        $prepared->bind_param("ss", $uuid, $folder_path);
                        $prepared->execute();
                        $prepared->close();
                        
                        $currentTime = date("Y-m-d H:i:s");
                        $actionQuery = $this->conn->prepare("INSERT INTO actions (User, Timestamp, IP) VALUES (?, ?, ?);");
                        if (!$actionQuery) {
                            die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                        }
                        $actionQuery->bind_param("sss", $uuid, $currentTime, $ip);
                        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                            $ip = $_SERVER['HTTP_CLIENT_IP'];
                        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                        } else {
                            $ip = $_SERVER['REMOTE_ADDR'];
                        }
                        $actionQuery->execute();
                        $conn->close();
                        setcookie("uuid", $uuid, time() + 21600, "/");
                        die(messagerArray_l3("Login", "Success", "You are now logged in"));
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
                $currentTime = date("Y-m-d H:i:s");

                $prepared = $this->conn->prepare("SELECT * FROM users WHERE Email = ?");
                $prepared->bind_param("s", $this->login);
                $prepared->execute();
                $prepared = $prepared->get_result();
                if ($prepared) {
                    if ($prepared->num_rows == 0) {
                        die(messagerArray_l3("Login", "Not found", "No such an email"));
                    } else {
                        while ($row = $prepared->fetch_assoc()) {
                            $dbPass = $row["Password"];
                            $uuid = $row["UUID"];
                        }
                        $prepared->close();

                        if (password_verify($this->password, $dbPass)) {
                            $actionQuery = $this->conn->prepare("INSERT INTO actions (User, Timestamp, IP) VALUES (?, ?, ?);");
                            if (!$actionQuery) {
                                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                            }
                            $actionQuery->bind_param("sss", $uuid, $currentTime, $ip);
                            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                                $ip = $_SERVER['HTTP_CLIENT_IP'];
                            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                            } else {
                                $ip = $_SERVER['REMOTE_ADDR'];
                            }
                            $actionQuery->execute();
                            $conn->close();
                            setcookie("uuid", $uuid, time() + 21600, "/");
                            die(messagerArray_l3("Login", "Success", "You are now logged in"));
                        } else {
                            die(messagerArray_l3("Login", "Not found", "Incorrect Password"));
                        }
                    }
                } else {
                    die(messagerArray_l3("Login", "Not found", "Occured unexpected error with data"));
                }
        } else {
            $prepared = $this->conn->prepare("SELECT * FROM users WHERE Login = ?");
            if(!$prepared){ //если ошибка - убиваем процесс и выводим сообщение об ошибке.
                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
            }
            $prepared->bind_param("s", $this->login);
            $prepared->execute();
            $prepared = $prepared->get_result();
            if ($prepared) {
                if ($prepared->num_rows == 1) {
                    while ($row = $prepared->fetch_assoc()) {
                        $dbPass = $row["Password"];
                        $uuid = $row["UUID"];
                    }
        
                    $prepared->close();
                    if (password_verify($this->password, $dbPass)) {
                        $actionQuery = $this->conn->prepare("INSERT INTO actions (User, Timestamp, IP) VALUES (?, ?, ?);");
                        if (!$actionQuery) {
                            die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                        }
                        $actionQuery->bind_param("sss", $uuid, $currentTime, $ip);
                        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                            $ip = $_SERVER['HTTP_CLIENT_IP'];
                        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                        } else {
                            $ip = $_SERVER['REMOTE_ADDR'];
                        }
                        $actionQuery->execute();
                        $conn->close();
                        setcookie("uuid", $uuid, time() + 21600, "/");
                        die(messagerArray_l3("Login", "Success", "You are now logged in"));
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
}

class dataChecker {
    private $conn;

    final public function loginChecker($loginToCheck) {

        $chat = new db_connect();
        $conn = $chat->connect();

        $this->conn = $conn;

        $select_userLogin = $this->conn->prepare("SELECT * FROM users WHERE Login = ?");
        if (!$select_userLogin) {
            die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
        }
        $select_userLogin->bind_param("s", $loginToCheck);
        $select_userLogin->execute();
        $res = $select_userLogin->get_result();
        if ($res->num_rows == 1) {
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
