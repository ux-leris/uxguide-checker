<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/itemDAO.class.php");
    require_once("../classes/checklist.class.php");

    session_start();

    $db = new Database;
    $conn = $db->connect();

    $id = $_POST["id"];

    $itemDAO = new ItemDAO;

    $checklist_id = $itemDAO->getChecklist($conn, $id);

    if($checklist_id) {
        $checklist = new Checklist;
        $checklist->loadChecklist($conn, $checklist_id);
        $isAuthor = $checklist->get_author() == $_SESSION["USER_ID"] ? true : false;
    }

    if(!$checklist_id || !$isAuthor) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

    if($itemDAO->delete_item($conn, $id))
    {
        echo 1;
    }
?>