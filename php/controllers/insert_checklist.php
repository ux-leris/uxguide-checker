<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/checklistDAO.class.php");
    require_once("../dataAccess/labelDAO.class.php");

    session_start();

    $title = $_POST["title"];
    $description = $_POST["description"];
    $author_id = $_SESSION["USER_ID"];

    $sectionTitles = $_POST["sectionTitles"];
    $itemLabels = $_POST["itemLabels"];
    $justifiableLabelsLocalIds = $_POST["hasJustification"];

    $db = new Database;
    $conn = $db->connect();

    $checklistDAO = new checklistDAO;
    $labelDAO = new LabelDAO;

    $checklist_id = $checklistDAO->insert_checklist($conn, $title, $description, $author_id);

    $position = 0;

    foreach($sectionTitles as $sectionTitle)
    {
        $checklistDAO->insert_section($conn, $checklist_id, $sectionTitle, $position);

        $position++;
    }

    $labelLocalId = 0;
    $i = 0;

    foreach($itemLabels as $labelTitle)
    {
        $hasJustification = false;

        if($justifiableLabelsLocalIds[$i] == $labelLocalId)
        {
            $hasJustification = true;
            $i++;
        }

        $labelDAO->insert_label($conn, $checklist_id, $labelTitle, $hasJustification);

        $labelLocalId++;
    }
    
    header("location: ../pages/checklistManager.php?c_id=${checklist_id}");
?>