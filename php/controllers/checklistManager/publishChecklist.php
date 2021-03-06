<?php
  require_once("../../classes/database.class.php");
  require_once("../../classes/checklist.class.php");

  session_start();

  $checklistId = $_POST["c_id"];

  $conn = Database::connect();

  $checklist = new Checklist($conn, $checklistId);

  $isAuthor = $checklist->getAuthorId() == $_SESSION["USER_ID"] ? true : false;

  if(!$isAuthor) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
  }

  $wasSuccess = false;
  
  if($_SESSION["USER_ID"] == $checklist->getAuthorId()) {
    $response = array();

    if($checklist->countChecklistItems($conn, $checklistId) > 0) {
      if($checklist->publish($conn)) {
        $response["status"] = "success";
        $response["message"] = "Your checklist has been successfully published.";

        $wasSuccess = true;
      } else {
        $response["message"] = "Internal server error.";
      }
    } else {
      $response["message"] = "You must add at least one item to your checklist before publish it.";
    }
  } else {
    $response["message"] = "You cannot add items to this checklist.";
  }

  if (!$wasSuccess) {
    $response["status"] = "error";
  }

  echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>