<?php
    require_once("../classes/database.class.php");
    require_once("../classes/label.class.php");
    require_once("../dataAccess/itemDAO.class.php");
    require_once("../dataAccess/evaluationDAO.class.php");

    session_start();

    $evaluation_id = $_GET["e_id"];

    $db = new Database;
    $conn = $db->connect();

    $evaluationDAO = new EvaluationDAO;
    $evaluationDAO->update_evaluation($conn, $evaluation_id);

    header("location: ../pages/checklistResult.php?e_id=".$evaluation_id);
?>