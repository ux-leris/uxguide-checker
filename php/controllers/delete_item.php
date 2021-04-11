<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/itemDAO.class.php");

    $db = new Database;
    $conn = $db->connect();

    $id = $_POST["id"];

    $itemDAO = new ItemDAO;

    if($itemDAO->delete_item($conn, $id))
    {
        echo 1;
    }
?>