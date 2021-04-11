<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/checklistDAO.class.php");

    $email = $_POST["email"];
    $checklist_id = $_GET["c_id"];

    $db = new Database;
    $conn = $db->connect();

    $checklistDAO = new ChecklistDAO;

    $checklistDAO->insert_access($conn, $email, $checklist_id);

    header("location: ../pages/checklistManager.php?c_id=".$checklist_id);
?>