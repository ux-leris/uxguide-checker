<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/itemDAO.class.php");
    require_once("../classes/checklist.class.php");

    session_start();

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

    $checklist_id = $itemDAO->getChecklist($conn, $id);

    $checklist = new Checklist;

    $checklist->loadChecklist($conn, $checklist_id);
    
    $isAuthor = $checklist->get_author() == $_SESSION["USER_ID"] ? true : false;

    if(!$checklist || !$isAuthor) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

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