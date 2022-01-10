<?php
  require_once("../../classes/database.class.php");
  require_once("../../dataAccess/itemDAO.class.php");

  session_start();

  $conn = Database::connect();

  $itemId = $_POST["itemId"];
  $pos = $_POST["newPosition"];

  $response = array();

  if (ItemDAO::updateItemPosition($conn, $itemId, $pos)) {
    $response["status"] = "success";
    $response["message"] = "Item position has been successfully updated.";
  } else {
    $response["status"] = "error";
    $response["message"] = "Internal server error.";
  }

  echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>