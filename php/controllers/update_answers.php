<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/itemDAO.class.php");

    $evaluation_id = $_POST["evaluation_id"];
    $item_id = $_POST["item_id"];

    $db = new Database;
    $conn = $db->connect();

    $itemDAO = new ItemDAO;

    if(!isset($_POST["label_id"]))
    {
        $justification = $_POST["justification"];
        if($justification == "") {
            $justification = NULL;
        }
        $itemDAO->update_itemJustificationAnswer($conn, $evaluation_id, $item_id, $justification);
    }
    else
    {
        $label_id = $_POST["label_id"];
        $itemDAO->update_itemLabelAnswer($conn, $evaluation_id, $item_id, $label_id);
    }
?>