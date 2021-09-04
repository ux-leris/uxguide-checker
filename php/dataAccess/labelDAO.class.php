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
    
    public function select_labelTitle($conn, $label_id)
    {
      $query = "select * from label where id = ?";

      $stmt = $conn->prepare($query);

      $stmt->bind_param("s", $label_id);
      $stmt->execute();

      $result = $stmt->get_result();

      $row = $result->fetch_assoc();

      if(!$row) {
          return NULL;
      }

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