<?php

    require_once("../../classes/database.class.php");
    require_once("../../dataAccess/itemDAO.class.php");
    require_once("../../dataAccess/labelDAO.class.php");

    $itemId = $_GET["i_id"];

    $conn = Database::connect();

    $itemDAO = new ItemDAO;

    $justifications = array();

    $result = $itemDAO->select_itemJustifications($conn, $itemId);

    while ($justification = $result->fetch_assoc()) {
        array_push($justifications, array(
            "label" => LabelDAO::getOptionTitle($conn, $justification["label"]),
            "text" => $justification["justification"]
        ));
    }

    echo json_encode($justifications);
?> 