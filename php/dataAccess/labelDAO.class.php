<?php
  class LabelDAO
  {
    public static function insertLabel($conn, $checklistId, $title, $needJustification)
    {
      $query = "INSERT INTO label(checklist_id, title, hasJustification) VALUES(?, ?, ?)";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("sss", $checklistId, $title, $needJustification);

      return $stmt->execute();
    }
    
    public static function getOptionTitle($conn, $labelId)
    {
      $query = "SELECT * FROM label WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $labelId);

      $stmt->execute();

      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      return $row["title"];
    }

    public function select_justifiableLabels($conn, $checklist_id)
    {
      $query = "select * from label where checklist_id = ? and hasJustification = true";

      $stmt = $conn->prepare($query);

      $stmt->bind_param("s", $checklist_id);
      $stmt->execute();

      return $stmt->get_result();
    }
  }
?>