<?php
  require_once("../../classes/database.class.php");
  require_once("../../classes/evaluation.class.php");
  require_once("../../dataAccess/evaluationDAO.class.php");

  session_start();

  $evaluationId = $_POST["e_id"];
  $timeElapsedInSeconds = $_POST["timeElapsedInSeconds"];

  $conn = Database::connect();

  $evaluation = new Evaluation($conn, $evaluationId);

  $isAuthor = $evaluation->getAuthorId() == $_SESSION["USER_ID"] ? true : false;

  if (!$evaluation || !$isAuthor) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
  }

  EvaluationDAO::updateEvaluationTime($conn, $evaluationId, $timeElapsedInSeconds);
?>