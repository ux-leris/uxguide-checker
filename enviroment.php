<?php

  // Setup your base URL and add this file to .gitignore
  class Enviroment {
    private static $baseURL = "http://localhost/applications/uxguide-checker";

    public static function getbaseURL() {
      return self::$baseURL;
    }
  }
  
?>