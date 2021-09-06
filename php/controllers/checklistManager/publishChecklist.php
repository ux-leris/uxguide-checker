<?php
  require_once("../../classes/database.class.php");
  require_once("../../classes/checklist.class.php");

  session_start();

  $checklistId = $_POST["c_id"];

  $conn = Database::connect();

  $checklist = new Checklist($conn, $checklistId);
  
  if($_SESSION["USER_ID"] == $checklist->getAuthorId()) {
    $response = array();

    if($checklist->countChecklistItems($conn, $checklistId) > 0) {
      $response["status"] = "success";
      $response["message"] = "Your checklist has been successfully published.";
    } else {
      $response["status"] = "error";
      $response["message"] = "You need add at least one checklist item before publish it.";
    }
  }

  echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>