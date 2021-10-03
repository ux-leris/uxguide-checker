<?php

    require_once("../classes/database.class.php");
    require_once("../classes/checklist.class.php");
    require_once("../dataAccess/evaluationDAO.class.php");
    require_once("../dataAccess/labelDAO.class.php");
    require_once("../dataAccess/chartColors.php");
    require_once("../dataAccess/chartPatterns.php");
    require_once("../../environment.php");

    $baseURL = Environment::getBaseURL();

    $db = new Database;
    $conn = $db->connect();

    $checklist_id = $_GET["c_id"];

    $checklist = new Checklist($conn, $checklist_id);

    // Get charts datas
    $curl = initCurl();

    $answersBySections = getAnswersBySectionsData($baseURL, $curl, $checklist_id);
    $overview = getOverviewData($baseURL, $curl, $checklist_id);
    $infoNumbers = getBigNumbers($baseURL, $curl, $checklist_id);

    $labels_count = sizeof($answersBySections["labels"]);

    closeCurl($curl);

    $sections = $answersBySections["sections"];

    $sections_count =  sizeof($answersBySections["sections"]);

    $average_time = formatEvaluationTime($infoNumbers['average_time']);

    $numberOfEvaluations = $overview["nEvaluations"];

    $labels = $overview["labels"];

    $answersByLabel = $overview["answersByLabel"];

    $justifiableLabels = [];
    $i=0;
    foreach($labels as $label) {
      if($label["hasJustification"] == true) {
        array_push($justifiableLabels, $i);
      }
      $i++;
    }

    $response = [
      "checklist_title" => $checklist->getTitle(),
      "answersBySections" => $answersBySections,
      "overview" => $overview,
      "infoNumbers" => $infoNumbers,
      "answersByLabel" => $answersByLabel,
      "labels" => $labels,
      "labels_count" => $labels_count,
      "justifiableLabels" => $justifiableLabels,
      "sections" => $sections,
      "sections_count" => $sections_count,
      "average_time" => $average_time,
      "numberOfEvaluations" => $numberOfEvaluations,
    ];

    $response = json_encode($response);
    header('Content-Type: application/json; charset=utf-8');
    echo $response;
    
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

    function getAnswersBySectionsData($baseURL, $curl, $checklist_id) {
      // Set the url
      $url = "$baseURL/php/api/answersBySections.php?c_id=$checklist_id";
      curl_setopt($curl, CURLOPT_URL, $url);

      // Get answers by sections
      $result_answers = curl_exec($curl);

      // Removing UTF-8 Bom 
      $result_answers = str_replace("\xEF\xBB\xBF",'',$result_answers); 

      // Decoding
      $answersBySections = json_decode($result_answers, true);

      return $answersBySections;
    }

    function getOverviewData($baseURL, $curl, $checklist_id) {
      $url = "$baseURL/php/api/overview.php?c_id=$checklist_id";
      curl_setopt($curl, CURLOPT_URL, $url);
  
      $resultOverview = curl_exec($curl);
      
      // Removing UTF-8 Bom 
      $resultOverview = str_replace("\xEF\xBB\xBF", "", $resultOverview); 
  
      // Decoding
      $overview = json_decode($resultOverview, true);

      return $overview;
    }

    function getBigNumbers($baseURL, $curl, $checklist_id) {
      $url = "$baseURL/php/api/infoNumbers.php?c_id=$checklist_id";
      curl_setopt($curl, CURLOPT_URL, $url);
    
      // Get info numbers
      $result_infoNumbers = curl_exec($curl);

      // Removing UTF-8 Bom 
      $result_infoNumbers = str_replace("\xEF\xBB\xBF",'',$result_infoNumbers); 
      
      // Decoding
      $infoNumbers = json_decode($result_infoNumbers, true);

      return $infoNumbers;
    }

    function formatEvaluationTime($averageTimeInSeconds) {
      $minutes = floor($averageTimeInSeconds / 60);
      $averageTimeInSeconds -= $minutes*60;
      $seconds = $averageTimeInSeconds;
      $average_time = sprintf("%02d:%02d", $minutes, $seconds);

      return $average_time;
    }

?>