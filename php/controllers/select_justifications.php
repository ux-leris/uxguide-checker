<?php

    require_once("../classes/database.class.php");
    require_once("../dataAccess/itemDAO.class.php");
    require_once("../dataAccess/labelDAO.class.php");

    $item_id = $_GET["i_id"];

    $db = new Database;
    $conn = $db->connect();

    $itemDAO = new ItemDAO;
    $labelDAO = new labelDAO;

    $justifications = array();

    $result = $itemDAO->select_itemJustifications($conn, $item_id);

    while ($justification = $result->fetch_assoc()) {
        array_push($justifications, array(
            "label" => $labelDAO->select_labelTitle($conn, $justification["label"]),
            "text" => $justification["justification"]
        ));
    }

    echo json_encode($justifications);
?>