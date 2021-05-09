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

    // If item_order is seted, then it is updating just the order.
    if(isset($_POST["item_order"])) {
        $itemDAO->update_order($conn, $id, $_POST["item_order"]);
        unset($_POST["item_order"]);
    } else {
        if($itemDAO->update_item($conn, $id, $text, $link)) {
            echo 1;
        }    
    }

    
?>