<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/userDAO.class.php");

    $email = $_POST["email"];
    $password = md5($_POST["password"]);

    $db = new Database;
    $conn = $db->connect();

    $userDAO = new userDAO;

    $userDAO->authenticate_user($conn, $email, $password);
?>