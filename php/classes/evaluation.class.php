<?php
    require_once("../dataAccess/evaluationDAO.class.php");
    require_once("../dataAccess/itemDAO.class.php");

    class Evaluation
    {
        public function insert_evaluation($conn, $checklist_id, $author_id)
        {
            $evaluationDAO = new EvaluationDAO;

            $evaluation_id = $evaluationDAO->insert($conn, $checklist_id, $author_id);

            $itemDAO = new ItemDAO;
            $itemDAO->insert($conn, $evaluation_id, $checklist_id);

            return $evaluation_id;
        }

        public function get_authorName($conn, $evaluation_id)
        {
            $evaluationDAO = new EvaluationDAO;

            $result = $evaluationDAO->select_authorName($conn, $evaluation_id);
            $row = $result->fetch_assoc();

            return $row["name"];
        }
    }
?>