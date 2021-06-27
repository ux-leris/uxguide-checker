<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/evaluationDAO.class.php");
    require_once("../classes/evaluation.class.php");

    session_start();

    $evaluation_id = $_POST["e_id"];
    $time = $_POST["seconds"];

    $db = new Database;
    $conn = $db->connect();

    $evaluation = new Evaluation;
    $evaluation->loadEvaluation($conn, $evaluation_id);

    $isAuthor = $evaluation->get_author() == $_SESSION["USER_ID"] ? true : false;

    if(!$evaluation || !$isAuthor) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

    $evaluationDAO = new EvaluationDAO;
    $evaluationDAO->update_time($conn, $evaluation_id, $time);
?>