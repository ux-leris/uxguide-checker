<?php
  class ItemDAO
  {
    public static function insertItem($conn, $checklistId, $sectionId, $itemTitle, $referenceLink)
    {
      $itemOrder = self::countItemsInSection($conn, $sectionId) + 1;

      $query = "INSERT INTO checklist_item(checklist_id, section_id, text, link, item_order) VALUES(?, ?, ?, ?, ?)";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("sssss", $checklistId, $sectionId, $itemTitle, $referenceLink, $itemOrder);

      return $stmt->execute();
    }

    private function countItemsInSection($conn, $sectionId) 
    {
      $query = "SELECT COUNT(*) AS counter FROM checklist_item WHERE section_id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $sectionId);

      $stmt->execute();

      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      return $row['counter'];
    }

    public static function insertItemsToEvaluate($conn, $evaluationId, $itemId)
    {
      $query = "INSERT INTO checklist_item_data(evaluation_id, checklist_item_id) VALUES(?, ?)";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss", $evaluationId, $itemId);

      return $stmt->execute();
    }

    public static function updateItemInfos($conn, $itemId, $itemTitle, $referenceLink)
    {
      $query = "UPDATE checklist_item SET text = ?, link = ? WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("sss", $itemTitle, $referenceLink, $itemId);

      return $stmt->execute();
    }

    public static function updateItemPosition($conn, $itemId, $position) 
    {
      $query = "UPDATE checklist_item SET item_order = ? WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss", $position, $itemId);

      return $stmt->execute();
    }

    public static function updateOptionAnswer($conn, $evaluationId, $itemId, $optionId)
    {
      $query = "UPDATE checklist_item_data SET label = ? WHERE evaluation_id = ? AND checklist_item_id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("sss", $optionId, $evaluationId, $itemId);

      $stmt->execute();
    }

    public static function updateJustificationAnswer($conn, $evaluationId, $itemId, $justification)
    {
      $query = "UPDATE checklist_item_data SET justification = ? WHERE evaluation_id = ? AND checklist_item_id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("sss", $justification, $evaluationId, $itemId);
      
      $stmt->execute();
    }

    public static function deleteItem($conn, $itemId)
    {
      $query = "DELETE FROM checklist_item WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $itemId);

      return $stmt->execute();
    }

    public static function getItemAnswer($conn, $evaluationId, $itemId)
    {
      $query = "SELECT * FROM checklist_item_data WHERE evaluation_id = ? AND checklist_item_id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss", $evaluationId, $itemId);
      
      $stmt->execute();

      return $stmt->get_result();
    }

      public function select_itemJustifications($conn, $item_id)
      {
          $query = "SELECT label, justification FROM checklist_item_data WHERE checklist_item_id = ? AND justification IS NOT NULL";

          $stmt = $conn->prepare($query);

          $stmt->bind_param('s', $item_id);
          $stmt->execute();

          return $stmt->get_result();
      }

    public static function getChecklistIdByItemId($conn, $itemId) 
    {
      $query = "SELECT checklist_id FROM checklist_item WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $itemId);
      
      $stmt->execute();

      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      return $row["checklist_id"];
    }
  }
?>
