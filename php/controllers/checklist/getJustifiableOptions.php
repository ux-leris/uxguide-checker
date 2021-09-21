<?php
  require_once("../../classes/database.class.php");
  require_once("../../classes/checklist.class.php");

  $checklistId = $_GET["c_id"];

  $conn = Database::connect();

  $checklist = new Checklist($conn, $checklistId);

  $isAuthor = $checklist->getAuthorId() == $_SESSION["USER_ID"] ? true : false;

  if(!$isAuthor) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
  }

  $justifiableOptions = Checklist::getJustifiableOptions($conn, $checklistId);

  echo json_encode($justifiableOptions);
?>