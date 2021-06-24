<?php 
  require_once("../classes/database.class.php");
  require_once("../classes/checklist.class.php");
  require_once("../classes/section.class.php");

  if(!isset($_GET["c_id"])) {
    http_response_code(404);
    exit();
  }

  try {
    $db = new Database;
    $conn = $db->connect();

    $checklist_id = $_GET["c_id"];
  
    $checklist = new Checklist;
    $section = new Section;

    $checklist->loadChecklist($conn, $checklist_id);

    if(!$checklist->get_id()) {
      http_response_code(404);
      exit();
    }

    $total_questions = $checklist->countItems($conn);

    $unfinished_evaluations = $checklist->countUnfinishedEvaluations($conn);

    $response = [
      "total_questions" => $total_questions,
      "unfinished_evaluations" => $unfinished_evaluations,
    ];

    $response = json_encode($response);
    header('Content-Type: application/json; charset=utf-8');
    echo $response;

  } catch(Exception $e) {
    http_response_code(404);
    exit();
  }


?>