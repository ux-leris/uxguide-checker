<?php
  require_once("../../classes/database.class.php");
  require_once("../../classes/checklist.class.php");
  require_once("../../dataAccess/itemDAO.class.php");

  session_start();

  $conn = Database::connect();

  $itemId = $_POST["itemId"];
  $itemTitle = $_POST["itemTitle"];
  $referenceLink = $_POST["referenceLink"];

  if ($referenceLink == "") $referenceLink == NULL;

  $checklist = new Checklist($conn, itemDAO::getChecklistIdByItemId($conn, $itemId));
  
  $isAuthor = $checklist->getAuthorId() == $_SESSION["USER_ID"] ? true : false;

  if(!$checklist || !$isAuthor) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
  }

  $response = array();

  if (itemDAO::updateItemInfos($conn, $itemId, $itemTitle, $referenceLink)) {
    $response["status"] = "success";
    $response["message"] = "Item has been successfully updated.";
  } else {
    $response["status"] = "error";
    $response["message"] = "Internal server error.";
  }

  echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>