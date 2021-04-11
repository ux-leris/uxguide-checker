<?php
    require_once("../classes/database.class.php");
    require_once("../classes/item.class.php");
    require_once("../dataAccess/itemDAO.class.php");

    $checklist_id = $_GET["c_id"];
    $section_id = $_GET["s_id"];

    $texts = $_POST["text"];
    $links = $_POST["link"];

    $db = new Database;
    $conn = $db->connect();

    $newItem = new Item;
    $itemDAO = new ItemDAO;

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