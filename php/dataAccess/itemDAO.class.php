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

    private function countItemsInSection($conn, $sectionId) {
      $query = "SELECT COUNT(*) AS counter FROM checklist_item WHERE section_id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $sectionId);

      $stmt->execute();

      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      return $row['counter'];
    }

      public function insert($conn, $evaluation_id, $checklist_id)
      {
          $query = "SELECT * FROM checklist_item WHERE checklist_id = ?";

          $stmt = $conn->prepare($query);

          $stmt->bind_param("s", $checklist_id);
          $stmt->execute();

          $result = $stmt->get_result();

          while($row = $result->fetch_assoc())
          {
              $query = "INSERT INTO checklist_item_data(evaluation_id, checklist_item_id) VALUES(".$evaluation_id.", ".$row["id"].")";

              $stmt = $conn->prepare($query);

              $stmt->execute();
          }
      }

    public static function updateItemInfos($conn, $itemId, $itemTitle, $referenceLink)
    {
      $query = "UPDATE checklist_item SET text = ?, link = ? WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("sss", $itemTitle, $referenceLink, $itemId);

      return $stmt->execute();
    }

      public function update_order($conn, $item_id, $item_order) {
          $query = "update checklist_item set item_order = ? where id = ?";

          $stmt = $conn->prepare($query);

          $stmt->bind_param("ss", $item_order, $item_id);

          return $stmt->execute();
      }

      public function update_itemLabelAnswer($conn, $evaluation_id, $item_id, $label_id)
      {
          $query = "UPDATE checklist_item_data SET label = ? WHERE evaluation_id = ? AND checklist_item_id = ?";

          $stmt = $conn->prepare($query);

          $stmt->bind_param("sss", $label_id, $evaluation_id, $item_id);
          $stmt->execute();
      }

      public function update_itemJustificationAnswer($conn, $evaluation_id, $item_id, $justification)
      {
          $query = "UPDATE checklist_item_data SET justification = ? WHERE evaluation_id = ? AND checklist_item_id = ?";

          $stmt = $conn->prepare($query);

          $stmt->bind_param("sss", $justification, $evaluation_id, $item_id);
          $stmt->execute();
      }

    public static function deleteItem($conn, $itemId)
    {
      $query = "DELETE FROM checklist_item WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $itemId);

      return $stmt->execute();
    }

      public function select_itemAnswer($conn, $evaluation_id, $item_id)
      {
          $query = "select * from checklist_item_data where evaluation_id = ".$evaluation_id." and checklist_item_id = ".$item_id;

          $stmt = $conn->prepare($query);

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

    public static function getChecklistIdByItemId($conn, $itemId) {
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
