<?php

    require_once("../classes/database.class.php");
    require_once("../dataAccess/itemDAO.class.php");

    $item_id = $_GET["i_id"];

    $db = new Database;
    $conn = $db->connect();

    $itemDAO = new ItemDAO;

    echo json_encode($itemDAO->select_itemJustifications($conn, $item_id));
?>