<?php
  class Database {
    private static $server = "127.0.0.1";
    private static $username = "u115699376_admin";
    private static $password = "uxguidechecker_Adm1n";
    private static $database = "u115699376_uxguidechecker";

    public static function connect() {
      $conn = new mysqli(self::$server, self::$username, self::$password, self::$database);

      $conn->set_charset("utf8");
      return $conn;
    }
  }
?>