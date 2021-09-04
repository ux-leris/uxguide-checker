<?php
  require_once("../../classes/database.class.php");
  require_once("../../dataAccess/checklistDAO.class.php");
  require_once("../../dataAccess/labelDAO.class.php");

  session_start();

  if(!$_SESSION["USER_ID"]) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
  }

  $title = $_POST["title"];
  $description = $_POST["description"];
  $authorId = $_SESSION["USER_ID"];

  $sectionTitles = $_POST["sectionTitles"];
  $optionTitles = $_POST["optionTitles"];

  $justificationLocalIds;

  if($_POST["needJustification"]) {
    $justificationLocalIds = $_POST["needJustification"];
  }

  $conn = Database::connect();

  $checklistId = checklistDAO::insertChecklist($conn, $title, $description, $authorId);

  $position = 0;

  foreach($sectionTitles as $sectionTitle)
  {
    checklistDAO::insertSection($conn, $checklistId, $sectionTitle, $position);
    $position++;
  }

  $i = 0;
  $optionLocalId = 0;

  foreach($optionTitles as $optionTitle)
  {
    $needJustification = false;

    if($justificationLocalIds && $justificationLocalIds[$optionLocalId] == $i)
    {
      $needJustification = true;
      $optionLocalId++;
    }

    labelDAO::insertLabel($conn, $checklistId, $optionTitle, $needJustification);

    $i++;
  }

  header("location: ../../pages/checklistManager.php?c_id=$checklistId");
?>