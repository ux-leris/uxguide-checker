<?php
    require_once("../classes/database.class.php");
    require_once("../classes/checklist.class.php");
    require_once("../classes/label.class.php");
    require_once("../classes/item.class.php");
    require_once("../classes/evaluation.class.php");

    if(!isset($_GET["c_id"]))
    {
        http_response_code(404);
        exit();
    }

    try
    {
        $conn = Database::connect();

        $checklist_id = $_GET["c_id"];

        $checklist = new Checklist($conn, $checklist_id);;

        if(!$checklist->getId())
        {
            http_response_code(404);
            exit();
        }

        $labels = array();

        $labelResult = Item::getItemOptions($conn, $checklist_id);

        while($labelRow = $labelResult->fetch_assoc())
            array_push($labels, array("id" => $labelRow["id"], "text" => $labelRow["title"], "hasJustification" => $labelRow["hasJustification"]));

        $answersByLabel = array();

        foreach($labels as $l)
            array_push($answersByLabel, Evaluation::countAnswersByLabel($conn, $l["id"]));

        $nEvaluations = Evaluation::countEvaluations($conn, $checklist_id);

        $response = [
            "nEvaluations" => $nEvaluations,
            "labels" => $labels,
            "answersByLabel" => $answersByLabel
        ];

        $response = json_encode($response);
        header("Content-Type: application/json; charset=utf-8");

        echo $response;
    }
    catch(Exception $err)
    {
        http_response_code(404);
        exit();
    }
?>