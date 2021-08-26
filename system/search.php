<?php 

if (isset($_GET["inputString"])) {
    $string = "%". $_GET["inputString"] ."%";
    $s = new userSearch($string);
} else {
    header("location: http://localhost/chat proto/");
}


class userSearch {
    private $string;
    private $conn;

    final public function __construct(string $str) {
        $this->string = $str;

        require("../system/includes/databases/chat.php");
        $chat = new db_connect();
        $this->conn = $chat->connect();
        
        $users = [];

        $select_user = $this->conn->prepare("SELECT Login, UUID FROM users WHERE Login LIKE ?");
        if (!$select_user) {
            die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
        }
        $select_user->bind_param("s", $this->string);
        $select_user->execute();
        $result = $select_user->get_result();

        $usersInfo_login = array();
        $userInfo_uuid = array();
        while($row = $result->fetch_assoc()) {
            $usersInfo_login[] = $row["Login"];
            $userInfo_uuid[] = $row["UUID"];
        }
        for ($i=0; $i<count($userInfo_uuid); $i++) {
            $select_avi = $this->conn->prepare("SELECT Filename FROM avis WHERE User = ? ORDER BY ID DESC LIMIT 1");
            if (!$select_avi) {
                die( "SQL Error: {$this->conn->errno} - {$this->conn->error}" );
            }
            $select_avi->bind_param("s", $userInfo_uuid[$i]);
            $select_avi->execute();
            if ($select_avi->get_result()->num_rows == 0) {
                $user_avi = "";
            } else {
                $row = $select_avi->get_result()->fetch_assoc();
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
            $user = array(
                "user" => $usersInfo_login[$i],
                "user_img" => $user_avi
            );
            array_push($users, $user);
        }

        $this->conn->close();
        die(json_encode($users));

    }
}
?>