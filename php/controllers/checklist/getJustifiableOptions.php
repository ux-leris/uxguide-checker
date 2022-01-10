<?php
  require_once("../../classes/database.class.php");
  require_once("../../classes/checklist.class.php");

  $checklistId = $_GET["c_id"];

  $conn = Database::connect();

  $justifiableOptions = Checklist::getJustifiableOptions($conn, $checklistId);

  echo json_encode($justifiableOptions);
?>