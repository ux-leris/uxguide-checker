<?php
  require_once("../../classes/database.class.php");
  require_once("../../classes/checklist.class.php");
  require_once("../../dataAccess/itemDAO.class.php");

  session_start();

  $conn = Database::connect();

  $itemId = $_POST["itemId"];

  $checklistId = itemDAO::getChecklistIdByItemId($conn, $itemId);

  $checklist = new Checklist($conn, $checklistId);
  $isAuthor = $checklist->getAuthorId() == $_SESSION["USER_ID"] ? true : false;

  if(!$checklistId || !$isAuthor) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
  }

  $response = array();

  if (itemDAO::deleteItem($conn, $itemId)) {
    $response["status"] = "success";
    $response["message"] = "Item has been successfully deleted.";
  } else {
    $response["status"] = "error";
    $response["message"] = "Internal server error.";
  }

  echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>