<?php
    require_once("../dataAccess/evaluationDAO.class.php");
    require_once("../dataAccess/itemDAO.class.php");

    class Evaluation
    {
        private $id;
        private $checklist_id;
        private $date;
        private $status;
        private $author;

        public function countEvaluations($conn, $checklist_id)
        {
            $evaluationDAO = new EvaluationDAO;
            
            $evaluationResult = $evaluationDAO->select_checklistEvaluationsQtd($conn, $checklist_id);
            $evaluationRow = $evaluationResult->fetch_assoc();

            return $evaluationRow["total"];
        }

        public function countAnswersByLabel($conn, $label_id)
        {
            $evaluationDAO = new EvaluationDAO;
            
            $evaluationResult = $evaluationDAO->select_answersByLabel($conn, $label_id);
            $evaluationRow = $evaluationResult->fetch_assoc();

            return $evaluationRow["total"];
        }

        public function loadEvaluation($conn, $evaluation_id)
        {
            $evaluationDAO = new EvaluationDAO;
            $evaluationResult = $evaluationDAO->select_evaluation($conn, $evaluation_id);
            $evaluationRow = $evaluationResult->fetch_assoc();

            if($evaluationRow) {
                $this->id = $evaluationRow["id"];
                $this->checklist_id = $evaluationRow["checklist_id"];
                $this->date = $evaluationRow["date"];
                $this->status = $evaluationRow['status'];
                $this->author = $evaluationRow['author'];
            } else {
                $this->id = NULL;
            }
        }

        public function insert_evaluation($conn, $checklist_id, $author_id)
        {
            $evaluationDAO = new EvaluationDAO;

            $evaluation_id = $evaluationDAO->insert($conn, $checklist_id, $author_id);

            $itemDAO = new ItemDAO;
            $itemDAO->insert($conn, $evaluation_id, $checklist_id);

            return $evaluation_id;
        }

        public function get_authorName($conn)
        {
            $evaluationDAO = new EvaluationDAO;

            $result = $evaluationDAO->select_authorName($conn, $this->id);
            $row = $result->fetch_assoc();

            return $row["name"];
        }

        public function get_id() {
            return $this->id;
        }

        public function get_checklist_id() {
            return $this->checklist_id;
        }

        public function get_date() {
            return $this->date;
        }

        public function get_status() {
            return $this->status;
        }

        public function get_author() {
            return $this->author;
        }
    }
?>