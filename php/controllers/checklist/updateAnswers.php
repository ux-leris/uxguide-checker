<?php
  require_once("../../classes/database.class.php");
  require_once("../../classes/label.class.php");
  require_once("../../classes/evaluation.class.php");
  require_once("../../dataAccess/itemDAO.class.php");

  session_start();

  $evaluationId = $_POST["e_id"];
  $itemId = $_POST["i_id"];

  $conn = Database::connect();

  $evaluation = new Evaluation($conn, $evaluationId);

  $isAuthor = $evaluation->getAuthorId() == $_SESSION["USER_ID"] ? true : false;

  if(!$evaluation || !$isAuthor) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
  }

  if (!isset($_POST["o_id"])) {
    $justification = $_POST["justification"];
    if ($justification == "") $justification = NULL;

    ItemDAO::updateJustificationAnswer($conn, $evaluationId, $itemId, $justification);
  } else {
    $optionId = $_POST["o_id"];
    ItemDAO::updateOptionAnswer($conn, $evaluationId, $itemId, $optionId);

    if (!Label::isJustifiableOption($conn, $optionId)) {
      ItemDAO::updateJustificationAnswer($conn, $evaluationId, $itemId, NULL);
    }
  }
?>