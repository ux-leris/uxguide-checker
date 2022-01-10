<?php 
  require_once("../classes/database.class.php");
  require_once("../classes/checklist.class.php");

  if(!isset($_GET["c_id"]) || !isset($_GET["section_id"]) || !isset($_GET["labels_number"])) {
    http_response_code(404);
    exit();
  }

  try {
    $conn = Database::connect();

    $checklist_id = $_GET["c_id"];
    $section_id = $_GET["section_id"];
    $labelsNumber = $_GET["labels_number"];

    $checklist = new Checklist($conn, $checklist_id);

    if(!$checklist->getId()) {
      http_response_code(404);
      exit();
    }

    $answers_count_result = $checklist->getNumberOfAnswersByQuestions($conn, $checklist_id, $section_id);

    $questionsNumber = $answers_count_result->num_rows / $labelsNumber;

    $questions_answers = array();
    for($i=0; $i<$questionsNumber; $i++) {
      $question_answer = array();
      for($j=0; $j<$labelsNumber; $j++) {
        $row = $answers_count_result->fetch_assoc();
        array_push($question_answer, $row["count"]);
      }
      $questions_answers[$row["item_id"]] = ["title" => $row['text'], "count" => $question_answer];
    }

    $response = [
      "questions_answers" => $questions_answers
    ];

    $response = json_encode($response);
    header('Content-Type: application/json; charset=utf-8');
    echo $response;

  } catch(Exception $e) {
    http_response_code(500);
    exit();
  }


?>