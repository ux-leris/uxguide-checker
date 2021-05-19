<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/checklistDAO.class.php");
    require_once("../classes/checklist.class.php");

    session_start();

    $email = $_POST["email"];
    $checklist_id = $_GET["c_id"];

    $db = new Database;
    $conn = $db->connect();

    $checklistDAO = new ChecklistDAO;
    $checklist = new Checklist;

    $checklist->loadChecklist($conn, $checklist_id);

    $isAuthor = $checklist->get_author() == $_SESSION["USER_ID"] ? true : false;

    if(!$isAuthor) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

    $checklistDAO->insert_access($conn, $email, $checklist_id);

    header("location: ../pages/checklistManager.php?c_id=".$checklist_id);
?>