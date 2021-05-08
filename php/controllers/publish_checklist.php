<?php
    require_once("../classes/database.class.php");
    require_once("../classes/checklist.class.php");

    session_start();

    $checklist_id = $_GET["c_id"];

    $db = new Database;
    $conn = $db->connect();

    $checklist = new Checklist;
    $checklist->loadChecklist($conn, $checklist_id);
    
    if($_SESSION["USER_ID"] == $checklist->get_author()) {
      $qt_items = $checklist->countItems($conn);
      
      if($qt_items == 0) {
        $response = array("error" => "You must add at least one question in your checklist");
      } else {
        if($checklist->publish($conn)) {
          $response = "success";
        } else {
          $response = array("error" => "An error occurred");
        }
      }

      $_SESSION['message'] = json_encode($response);
      header("location: ../pages/checklistManager.php?c_id=${checklist_id}");
      exit();
    }

    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();

?>