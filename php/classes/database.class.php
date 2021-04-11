<?php
    class Database
    {
        private $server = "127.0.0.1";
        private $username = "root";
        private $password = "";
        private $database = "checklist_tool";

        public function connect()
        {
            $conn = new mysqli($this->server, $this->username, $this->password, $this->database);

            if($conn->connect_error)
            {
                echo "Connection failed.";
            }
            else
            {
                $conn->set_charset("utf8");
                return $conn;
            }
        }
    }
?>