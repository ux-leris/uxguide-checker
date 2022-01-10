<?php
  require_once("../../classes/database.class.php");
  require_once("../../dataAccess/userDAO.class.php");

  $name = $_POST["name"];
  $email = $_POST["email"];
  $password = md5($_POST["password"]);

  $conn = Database::connect();

  if (userDAO::signUp($conn, $name, $email, $password)) {
    header("location: ../../pages/signIn.php");
  } else {
    header("location: ../../pages/signUp.php");
  }
?>