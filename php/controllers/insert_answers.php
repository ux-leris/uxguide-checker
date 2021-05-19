<?php
    require_once("../classes/database.class.php");
    require_once("../classes/evaluation.class.php");
    require_once("../dataAccess/evaluationDAO.class.php");

    session_start();

    $evaluation_id = $_GET["e_id"];

    $db = new Database;
    $conn = $db->connect();

    $evaluationDAO = new EvaluationDAO;
    $evaluation = new Evaluation;

    $evaluation->loadEvaluation($conn, $evaluation_id);

    $isAuthor = $evaluation->get_author() == $_SESSION["USER_ID"] ? true : false;

    if(!$evaluation || !$isAuthor) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

    $evaluationDAO->update_evaluation($conn, $evaluation_id);

    header("location: ../pages/checklistResult.php?e_id=".$evaluation_id);
?>