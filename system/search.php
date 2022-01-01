<?php 

if (isset($_GET["inputString"])) {
    $string = $_GET["inputString"]."%";
    $s = new userSearch($string);
} else {
    header("location: http://localhost/chat proto/");
}


class userSearch {
    private $string;
    private $conn;

    final public function __construct(string $str) {
        $this->string = $str;
        if (substr($this->string, 0, 1) !== "@") {
            require("../system/includes/databases/chat.php");
            $chat = new db_connect();
            $this->conn = $chat->connect();
            
            $users = [];
    
            $select_user = $this->conn->prepare("SELECT uuid, first_name, last_name FROM usernames WHERE uuid NOT IN (?) AND (first_name LIKE ? OR last_name LIKE ?) LIMIT 3;");
            if (!$select_user) {
                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
            }
            $select_user->bind_param("sss", $uuid, $this->string, $this->string);
            $uuid=hex2bin($_COOKIE["uuid"]);
            $select_user->execute();
            $result = $select_user->get_result();
    
            $usersInfo_username = array();
            $userInfo_uuid = array();
            while($row = $result->fetch_assoc()) {
                $usersInfo_username[] = $row["first_name"]." ".$row["last_name"];
                $userInfo_uuid[] = $row["uuid"];
            }
            for ($i=0; $i<count($userInfo_uuid); $i++) {
                $select_avi = $this->conn->prepare("SELECT Filename FROM avis WHERE User = ? ORDER BY ID DESC LIMIT 1");
                if (!$select_avi) {
                    die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                }
                $select_avi->bind_param("s", $userInfo_uuid[$i]);
                $select_avi->execute();
                $res =$select_avi->get_result();
                if ($res->num_rows == 0) {
                    $user_avi = "";
                } else {
                    $row = $res->fetch_assoc();
                    $user_lastAvi = $row["Filename"];
    
                    $select_path = $this->conn->prepare("SELECT Path FROM folders WHERE User = ?");
                    if (!$select_path) {
                        die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                    }
                    $select_path->bind_param("s", $userInfo_uuid[$i]);
                    $select_path->execute();
                    $row = $select_path->get_result()->fetch_assoc();
                    $user_path = $row["Path"];
                    $user_avi = $user_path .'photos/avis/'. $user_lastAvi;
                } 
                
                $select_sUN = $this->conn->prepare("SELECT Login FROM u_info WHERE UUID = ?");
                if (!$select_sUN) {
                    die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
                }
                $select_sUN->bind_param("s", $userInfo_uuid[$i]);
                $select_sUN->execute();
                $res = $select_sUN->get_result();
                $row = $res->fetch_assoc();
                $userInfo_sUN = "";
                $userInfo_sUN=$row["Login"];
                
                $user = array(
                    "username_full" => $usersInfo_username[$i],
                    "username_shorted" => $userInfo_sUN,
                    "u_uuid" => bin2hex($userInfo_uuid[$i]),
                    "user_img" => $user_avi
                );
                array_push($users, $user);
            }
    
            $this->conn->close();
            die(json_encode($users));
        }


    }
}
?>