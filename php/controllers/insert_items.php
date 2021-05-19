<?php
    require_once("../classes/database.class.php");
    require_once("../classes/item.class.php");
    require_once("../classes/checklist.class.php");
    require_once("../dataAccess/itemDAO.class.php");

    session_start();

    $checklist_id = $_GET["c_id"];
    $section_id = $_GET["s_id"];

    $texts = $_POST["text"];
    $links = $_POST["link"];

    $db = new Database;
    $conn = $db->connect();

    $newItem = new Item;
    $itemDAO = new ItemDAO;
    $checklist = new Checklist;
    
    $checklist->loadChecklist($conn, $checklist_id);
    
    $isAuthor = $checklist->get_author() == $_SESSION["USER_ID"] ? true : false;

    if(!$checklist || !$isAuthor) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

    $i = 0;

    foreach($texts as $itemText)
    {
        if($links[$i] != '')
            $link = $links[$i];
        else
            $link = NULL;

        $itemDAO->insert_item($conn, $checklist_id, $section_id, $itemText, $link);

        $i++;
    }

    header("location: ../pages/sectionEditor.php?id=".$section_id);
?>