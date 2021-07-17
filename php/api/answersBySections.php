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

    $sections_result = $checklist->loadSectionsOfChecklist($conn, $checklist_id);
    $labels_result = $checklist->loadLabelsOfChecklist($conn, $checklist_id);
    $answers_count_result = $checklist->getNumberOfAnswersBySections($conn, $checklist_id);

    $sectionsNumber = $sections_result->num_rows;
    $labelsNumber = $labels_result->num_rows;

    $sections = array();
    $sections_ids = array();
    while($section = $sections_result->fetch_assoc()) {
      array_push($sections, $section["title"]);
      array_push($sections_ids, $section["id"]);
    }

    $labels = array();
    while($label = $labels_result->fetch_assoc()) {
      array_push($labels, $label["title"]);
    }

    $answers = array();
    for($i=0; $i<$sectionsNumber; $i++) {
      $section_answers = array();
      for($j=0; $j<$labelsNumber; $j++) {
        $row = $answers_count_result->fetch_assoc();
        array_push($section_answers, $row["qty_answers"]);
      }
      array_push($answers, $section_answers);
    }

    $response = [
      "checklist" => $checklist->get_title(),
      "sections" => $sections,
      "sections_ids" => $sections_ids,
      "labels" => $labels,
      "answers" => $answers
    ];

    $response = json_encode($response);
    header('Content-Type: application/json; charset=utf-8');
    echo $response;

  } catch(Exception $e) {
    http_response_code(404);
    exit();
  }


?>