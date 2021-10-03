<?php

  require_once("../classes/database.class.php");
  require_once("../classes/checklist.class.php");
  require_once("../../environment.php");

  session_start();

  $baseURL = Environment::getBaseURL();

  if(!isset($_GET["c_id"])) {
    header("HTTP/1.0 404 Not found");
    echo "<h1>404 Not found</h1>";
    echo "This page doesn't exist";
    exit();
  }

  $conn = Database::connect();

  $checklist_id = $_GET["c_id"];

  $checklist = new Checklist($conn, $checklist_id);

  if(!$checklist->getId()) {
    header("HTTP/1.0 404 Not found");
    echo "<h1>404 Not found</h1>";
    echo "This page doesn't exist";
    exit();
  }
  
  $arquivo = fopen('php://output', 'rw');


  $filename = "checklist-$checklist_id";


  header('Content-type: text/csv; charset=UTF-8');
  header("Content-disposition: attachment; filename={$filename}.csv");
  header("Pragma: no-cache");

  try {

    // Cabeçalho do csv
    $info_headers = ['Title', 'Total questions', 'Total evaluations',	'Finished evaluations',	'Unfinished evaluations',	'Average time to evalute (MM:SS)'];

    fprintf($arquivo, chr(0xEF).chr(0xBB).chr(0xBF));
  
    // Criar o cabeçalho
    fputcsv($arquivo , $info_headers, ";");
  
    $data = getAllData($baseURL, $checklist_id);
  
    extract($data);
  
    $checklist_infos = [
      $checklist_title,
      $infoNumbers["total_questions"],
      $numberOfEvaluations,
      $infoNumbers["total_finished_evaluations"],
      $infoNumbers["total_unfinished_evaluations"],
      $average_time
    ];
  
    fputcsv($arquivo, $checklist_infos, ";");
    fputcsv($arquivo, []);


    if($infoNumbers["total_finished_evaluations"] > 0) {
      fputcsv($arquivo, ['Number of answers by options'], ";");
      fputcsv($arquivo, ['Option name', 'Total'], ";");
    
      $label_names = [];
    
      $i = 0;
      foreach($labels as $label) {
        array_push($label_names, $label['text']);
        fputcsv($arquivo, [$label['text'], $answersByLabel[$i]], ";");
        $i++;
      }
      fputcsv($arquivo, []);
    
      fputcsv($arquivo, ["Number of answers by sections"], ";");
      fputcsv($arquivo, array_merge(["Section names"], $label_names), ";");
    
      $i = 0;
      foreach($sections as $section) {
        fputcsv($arquivo, array_merge([$section], $answersBySections['answers'][$i]), ";");
        $i++;
      }
      fputcsv($arquivo, []);

      fputcsv($arquivo, ["Number of answers by questions"], ";");

      // $i=0;
      // $labelsAndJustifications = [];
      // foreach($labels as $label) {
      //   if($label['hasJustification']) {
      //     array_push($labelsAndJustifications, $label['text']);
      //     array_push($labelsAndJustifications, "Justifications");
      //   } else {
      //     array_push($labelsAndJustifications, $label['text']);
      //   }
      //   $i++;
      // }
      // fputcsv($arquivo, array_merge(["Question"], $labelsAndJustifications), ";");
      
      $i=1;
      foreach($answersByQuestions as $section_questions) {
        fputcsv($arquivo, ["Section $i"], ";");
        fputcsv($arquivo, array_merge(["Question"], $label_names), ";");
        foreach($section_questions["questions_answers"] as $question) {
          $line = [];
          array_push($line, $question['title']);
          foreach($question['count'] as $count) {
            array_push($line, $count);
          }
          fputcsv($arquivo, $line, ";");
        }
        fputcsv($arquivo, []);
        $i++;
      }
      fputcsv($arquivo, []);
    }

  } catch(\Exception $e) {
    $_SESSION["error"] = "An error ocurred when generating your checklist data.";
    header("Location: $baseURL/php/pages/checklistEvaluations.php?c_id=$checklist_id");
    die();
  }
  
?>

<?php 

    function initCurl() {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPGET, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json'
      ));

      return $ch;
    }

    function closeCurl($curl) {
      // Closing
      curl_close($curl);
    }

    function getAnalyticsData($baseURL, $curl, $checklist_id) {
      // Set the url
      $url = "$baseURL/php/api/getAnalyticsData.php?c_id=$checklist_id";
      curl_setopt($curl, CURLOPT_URL, $url);

      // Get answers by sections
      $result_answers = curl_exec($curl);

      // Removing UTF-8 Bom 
      $result_answers = str_replace("\xEF\xBB\xBF",'',$result_answers); 

      // Decoding
      $analyticsData = json_decode($result_answers, true);

      return $analyticsData;
    }

    function getAnswersByQuestionsData($baseURL, $curl, $checklist_id, $section, $labels_count) {
      $url = "$baseURL/php/api/answersByQuestions.php?section_id=$section&labels_number=$labels_count&c_id=$checklist_id";

      curl_setopt($curl, CURLOPT_URL, $url);
    
      // Get answers by questions (first section)
      $result_answersByQuestions = curl_exec($curl);
  
      // Removing UTF-8 Bom 
      $result_answersByQuestions = str_replace("\xEF\xBB\xBF",'',$result_answersByQuestions); 

      // Decoding
      $answersByQuestions = json_decode($result_answersByQuestions, true);

      return $answersByQuestions;
    }

    function getAllData($baseURL, $checklist_id) {
      $curl = initCurl();

      $analytics = getAnalyticsData($baseURL, $curl, $checklist_id);

      $answersByQuestions = [];

      foreach($analytics["answersBySections"]["sections_ids"] as $section_id) {
        $answersByQuestionsSection = getAnswersByQuestionsData($baseURL, $curl, $checklist_id, $section_id, $analytics["labels_count"]);
        array_push($answersByQuestions, $answersByQuestionsSection);
      }

      closeCurl($curl);

      $analytics["answersByQuestions"] = $answersByQuestions;

      return $analytics;
    }

?>