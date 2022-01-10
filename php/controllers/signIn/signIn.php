<?php
  require_once("../../classes/database.class.php");
  require_once("../../dataAccess/userDAO.class.php");

  $email = $_POST["email"];
  $password = md5($_POST["password"]);

  $conn = Database::connect();

  $stmt = userDAO::signIn($conn, $email, $password);
  
  if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $name, $email, $password);
    $stmt->fetch();

    session_start();

    $_SESSION["USER_ID"] = $id;

    header("location: ../../../index.php");
  } else {
    session_start();
    $_SESSION["ERROR"] = "Email ou senha inválidos";
    header("location: ../../pages/signIn.php");
  }
?>