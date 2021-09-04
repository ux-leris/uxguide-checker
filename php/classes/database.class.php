<?php
  class Database {
    private static $server = "127.0.0.1";
    private static $username = "root";
    private static $password = "";
    private static $database = "checklist_tool";

    public static function connect() {
      $conn = new mysqli(self::$server, self::$username, self::$password, self::$database);

      $conn->set_charset("utf8");
      return $conn;
    }
  }
?>