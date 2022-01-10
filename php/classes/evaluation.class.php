<?php
  require_once(__DIR__."/item.class.php");

  require_once(__DIR__."/../dataAccess/evaluationDAO.class.php");
  require_once(__DIR__."/../dataAccess/itemDAO.class.php");

  class Evaluation
  {
    private $id;
    private $checklistId;
    private $date;
    private $status;
    private $authorId;
    private $timeElapsedInSeconds;

    public function __construct($conn, $evaluationId)
    {
      $evaluationResult = EvaluationDAO::getEvaluationInfos($conn, $evaluationId);
      $evaluationRow = $evaluationResult->fetch_assoc();

      if ($evaluationRow) {
        $this->id = $evaluationRow["id"];
        $this->checklistId = $evaluationRow["checklist_id"];
        $this->date = $evaluationRow["date"];
        $this->status = $evaluationRow['status'];
        $this->authorId = $evaluationRow['author'];
        $this->timeElapsedInSeconds = $evaluationRow['time_elapsed'];
      } else {
        $this->id = NULL;
      }
    }

      public static function countEvaluations($conn, $checklist_id)
      {
          $evaluationDAO = new EvaluationDAO;
          
          $evaluationResult = $evaluationDAO->select_checklistEvaluationsQtd($conn, $checklist_id);
          $evaluationRow = $evaluationResult->fetch_assoc();

          return $evaluationRow["total"];
      }

      public static function countAnswersByLabel($conn, $label_id)
      {
          $evaluationDAO = new EvaluationDAO;
          
          $evaluationResult = $evaluationDAO->select_answersByLabel($conn, $label_id);
          $evaluationRow = $evaluationResult->fetch_assoc();

          return $evaluationRow["total"];
      }

    public static function insertEvaluation($conn, $checklistId, $authorId)
    {
      $evaluationId = EvaluationDAO::insertEvaluation($conn, $checklistId, $authorId);

      Item::insertItemsToEvaluate($conn, $checklistId, $evaluationId);

      return $evaluationId;
    }

      public function get_authorName($conn)
      {
          $evaluationDAO = new EvaluationDAO;

          $result = $evaluationDAO->select_authorName($conn, $this->id);
          $row = $result->fetch_assoc();

          return $row["name"];
      }

    public function getId() {
      return $this->id;
    }

    public function getChecklistId() {
      return $this->checklistId;
    }

    public function getDate() {
      return $this->date;
    }

    public function getStatus() {
      return $this->status;
    }

    public function getAuthorId() {
      return $this->authorId;
    }

    public function getTimeElapsedInSeconds() {
      return $this->timeElapsedInSeconds;
    }
  }
?>