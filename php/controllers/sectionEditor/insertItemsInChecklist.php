<?php
  require_once("../../classes/database.class.php");
  require_once("../../classes/checklist.class.php");
  require_once("../../dataAccess/itemDAO.class.php");

  session_start();

  $checklistId = $_GET["c_id"];
  $sectionId = $_GET["s_id"];

  $itemTitles = $_POST["itemTitles"];
  $referenceLinks = $_POST["referenceLinks"];

  $conn = Database::connect();

  $checklist = new Checklist($conn, $checklistId);

  $isAuthor = $checklist->getAuthorId() == $_SESSION["USER_ID"] ? true : false;

  if (!$checklist || !$isAuthor) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
  }

  $i = 0;

  foreach($itemTitles as $itemTitle) {
    if ($referenceLinks[$i] != "") $referenceLink = $referenceLinks[$i];
    else $referenceLink = NULL;

    itemDAO::insertItem($conn, $checklistId, $sectionId, $itemTitle, $referenceLink);

    $i++;
  }

  header("location: ../../pages/sectionEditor.php?s_id=$sectionId");
?>