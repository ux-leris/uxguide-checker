<?php
  class userDAO
  {
    public static function signUp($conn, $name, $email, $password)
    {
      $query = "INSERT INTO user(name, email, password) VALUES(?, ?, ?)";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("sss", $name, $email, $password);

      return $stmt->execute();
    }

    public static function signIn($conn, $email, $password)
    {
      $query = "SELECT * FROM user WHERE email = ? AND password = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss", $email, $password);

      $stmt->execute();
      $stmt->store_result();

      return $stmt;
    }
  }
?>