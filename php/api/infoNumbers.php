<?php 
  require_once("../classes/database.class.php");
  require_once("../classes/checklist.class.php");
  require_once("../classes/section.class.php");

  if(!isset($_GET["c_id"])) {
    http_response_code(404);
    exit();
  }

  try {
    $conn = Database::connect();

    $checklist_id = $_GET["c_id"];
  
    $checklist = new Checklist($conn, $checklist_id);;

    if(!$checklist->getId()) {
      http_response_code(404);
      exit();
    }

    $total_questions = $checklist->countChecklistItems($conn);

    $evaluations = $checklist->loadEvaluations($conn);

    $unfinished_evaluations = 0;
    $finished_evaluations = array();
    $average_time = 0;
    $count_avg = 0;
    while($evaluation = $evaluations->fetch_assoc()) {
      if($evaluation['status'] == 1) {
        array_push($finished_evaluations, $evaluation['time_elapsed']);
        if($evaluation['time_elapsed']) {
          $count_avg++;
          $average_time += $evaluation['time_elapsed'];
        }
      } else {
        $unfinished_evaluations++;
      }
    }

    if($count_avg != 0) $average_time /= $count_avg;

    $response = [
      "total_questions" => $total_questions,
      "total_evaluations" => $evaluations->num_rows,
      "total_finished_evaluations" => sizeof($finished_evaluations),
      "total_unfinished_evaluations" => $unfinished_evaluations,
      "finished_evaluations" => $finished_evaluations,
      "average_time" => $average_time
    ];

    $response = json_encode($response);
    header('Content-Type: application/json; charset=utf-8');
    echo $response;

  } catch(Exception $e) {
    http_response_code(404);
    exit();
  }


?>