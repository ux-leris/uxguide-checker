<?php
    require_once("../classes/database.class.php");
    require_once("../classes/label.class.php");
    require_once("../dataAccess/itemDAO.class.php");
    require_once("../dataAccess/evaluationDAO.class.php");

    session_start();

    $author = $_SESSION["USER_ID"];

    $checklist_id = $_GET["c_id"];
    $evaluation_id = $_GET["e_id"];

    $items_ids = $_POST["id"];
    $labels = $_POST["label"];

    if(isset($_POST["justification"]))
    {
        $justifications = $_POST["justification"];
    }

    $justification = NULL;
    $jusCounter = 0;

    $db = new Database;
    $conn = $db->connect();

    $evaluationDAO = new EvaluationDAO;
    $evaluationDAO->update_evaluation($conn, $evaluation_id);

    header("location: ../pages/checklistResult.php?e_id=".$evaluation_id);
?>