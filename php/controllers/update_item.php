<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/itemDAO.class.php");

    $db = new Database;
    $conn = $db->connect();

    $id = $_POST["id"];
    $text = $_POST["text"];
    $link = $_POST["link"];

    if($link == "")
    {
        $link = NULL;
    }

    $itemDAO = new ItemDAO;

    if($itemDAO->update_item($conn, $id, $text, $link))
    {
        echo 1;
    }
?>