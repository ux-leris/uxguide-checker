<?php
  require_once("../../classes/database.class.php");
  require_once("../../dataAccess/checklistDAO.class.php");
  require_once("../../classes/checklist.class.php");

  session_start();

  $checklistId = $_GET["c_id"];
  $email = $_POST["email"];

  $conn = Database::connect();

  $checklist = new Checklist($conn, $checklistId);

  $isAuthor = $checklist->getAuthorId() == $_SESSION["USER_ID"] ? true : false;

  if(!$isAuthor) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
  }

  ChecklistDAO::shareChecklist($conn, $email, $checklistId);

  header("location: ../../pages/checklistManager.php?c_id=$checklistId");
?>