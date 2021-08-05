<?php 

class db_connect {
    protected $host = "localhost";
    protected $login = "root";
    protected $password = "";
    protected $database = "";

    final public function connect($database = "chat") {
        $conn = mysqli_connect($this->host, $this->login, $this->password, $database);
        if (!$conn) {
            die("Error occured: ". mysqli_connect_errno());
        } else {
            return $conn;
        }
    }
}

?>