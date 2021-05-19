<?php
    require_once("../classes/database.class.php");
    require_once("../dataAccess/itemDAO.class.php");
    require_once("../classes/evaluation.class.php");

    session_start();

    $evaluation_id = $_POST["evaluation_id"];
    $item_id = $_POST["item_id"];

    $db = new Database;
    $conn = $db->connect();

    $itemDAO = new ItemDAO;

    $evaluation = new Evaluation;
    $evaluation->loadEvaluation($conn, $evaluation_id);

    $isAuthor = $evaluation->get_author() == $_SESSION["USER_ID"] ? true : false;

    if(!$evaluation || !$isAuthor) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

    if(!isset($_POST["label_id"]))
    {
        $justification = $_POST["justification"];
        if($justification == "") {
            $justification = NULL;
        }
        $itemDAO->update_itemJustificationAnswer($conn, $evaluation_id, $item_id, $justification);
    }
    else
    {
        $label_id = $_POST["label_id"];
        $itemDAO->update_itemLabelAnswer($conn, $evaluation_id, $item_id, $label_id);
    }
?>