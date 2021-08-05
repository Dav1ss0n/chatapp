<?php 

class sessionChecker {
    private $entranceTime;
    private $conn;
    private $user;

    final public function __construct($value1, $user) {
        $this->entranceTime = $value1;
        $this->user = $user;

        require("/xampp/htdocs/chat proto/system/databases/chat.php");
        $chat = new db_connect;
        $conn = $chat->connect();
        $this->conn = $conn;

        $query = $this->conn->query("SELECT * FROM actions WHERE User = '$this->user' ORBER BY ID DESC LIMIT 1");
        if ($query) {
            while ($row = $query->fetch_assoc()) {
                $lastEntrance = $row["Timestamp"];
                $lastEntranceID = $row["ID"];
            }

            if (strtotime($this->entranceTime) > strtotime($lastEntrance)+86400*6) {
                $array = array(
                    "Session" => $lastEntranceID,
                    "Status" => "Expired"
                );
                echo json_encode($array);
            } else {
                $array = array(
                    "Session" => $lastEntranceID,
                    "Status" => "Not expired",
                    "Deadline" => date("Y-m-d H:i:s", time()+86400*6)
                );
                echo json_encode($array);
            }
        }
        
    }
}
?>