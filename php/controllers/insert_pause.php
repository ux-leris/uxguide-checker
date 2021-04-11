<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/evaluationDAO.class.php");

    $evaluation_id = $_POST["e_id"];
    $time = $_POST["sec"];

    $db = new Database;
    $conn = $db->connect();

    $evaluationDAO = new EvaluationDAO;
    $evaluationDAO->insert_pause($conn, $evaluation_id, $time);
?>