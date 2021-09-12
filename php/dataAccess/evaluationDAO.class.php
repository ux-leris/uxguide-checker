<?php
  class EvaluationDAO
  {
    public static function insertEvaluation($conn, $checklistId, $authorId)
    {
      $query = "INSERT INTO evaluation(checklist_id, author, status, time_elapsed) VALUES(?, ?, FALSE, 0)";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss", $checklistId, $authorId);

      $stmt->execute();

      return $stmt->insert_id;
    }

      public function insert_evaluation($conn, $checklist_id, $author)
      {
          $query = "INSERT INTO evaluation(checklist_id, author) VALUES(".$checklist_id.", '".$author."')";

          $stmt = $conn->prepare($query);

          $stmt->execute();

          return $stmt->insert_id;
      }

      public function insert_pause($conn, $evaluation_id, $time)
      {
          $query = "INSERT INTO pause(evaluation_id, time) VALUES(".$evaluation_id.", ".$time.")";

          $stmt = $conn->prepare($query);

          $stmt->execute();
      }

    public static function updateEvaluationTime($conn, $evaluationId, $timeElapsedInSeconds)
    {
      $query = "UPDATE evaluation SET time_elapsed = time_elapsed + ? WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss", $timeElapsedInSeconds, $evaluationId);

      return $stmt->execute();
    }

    public static function finishEvaluation($conn, $evaluationId)
    {
      $query = "UPDATE evaluation SET status = true WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $evaluationId);

      return $stmt->execute();
    }

      public function select_answersByLabel($conn, $label_id)
      {
          $query = "select count(*) as total from checklist_item_data as check_data, evaluation as eval, label as lab where check_data.evaluation_id = eval.id and check_data.label = lab.id and lab.id = ?";

          $stmt = $conn->prepare($query);

          $stmt->bind_param("s", $label_id);
          $stmt->execute();

          return $stmt->get_result();
      }

    public static function getEvaluationInfos($conn, $evaluationId)
    {
      $query = "SELECT * FROM evaluation WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $evaluationId);

      $stmt->execute();

      return $stmt->get_result();
    }

    public static function getEvaluationsOfChecklistByUser($conn, $checklistId, $userId)
    {
      $query = "SELECT * FROM evaluation WHERE checklist_id = ? AND (status = true or author = ?) ORDER BY date DESC";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss", $checklistId, $userId);

      $stmt->execute();

      return $stmt->get_result();
    }

      public function select_checklistEvaluationsQtd($conn, $checklist_id)
      {
          $query = "select count(*) as total from evaluation where checklist_id = ?";

          $stmt = $conn->prepare($query);

          $stmt->bind_param("s", $checklist_id);
          $stmt->execute();

          return $stmt->get_result();
      }

      public function select_evaluationsOfUser($conn, $checklist_id, $user_id)
      {
          $query = "select * from evaluation where checklist_id = ? and author = ? order by date desc";

          $stmt = $conn->prepare($query);

          $stmt->bind_param("ss", $checklist_id, $user_id);
          $stmt->execute();

          return $stmt->get_result();
      }

    public static function getAuthorName($conn, $evaluationId)
    {
      $query = "SELECT u.name FROM evaluation AS e, user AS u WHERE e.id = ? AND author = u.id";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $evaluationId);

      $stmt->execute();

      return $stmt->get_result();
    }
  }
?>