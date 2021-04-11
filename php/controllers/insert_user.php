<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/userDAO.class.php");

    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = md5($_POST["password"]);

    $db = new Database;
    $conn = $db->connect();

    $userDAO = new userDAO;

    $userDAO->insert_user($conn, $name, $email, $password);

    header("location: ../pages/login.php");
?>