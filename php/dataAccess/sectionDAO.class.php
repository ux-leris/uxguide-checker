<?php
  class SectionDAO
  {
    public static function getSectionInfos($conn, $sectionId)
    {
      $query = "SELECT * FROM section WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $sectionId);

      $stmt->execute();

      return $stmt->get_result();
    }

    public static function getSectionItems($conn, $sectionId)
    {
      $query = "SELECT * FROM checklist_item WHERE section_id = ? ORDER BY item_order";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $sectionId);

      $stmt->execute();

      return $stmt->get_result();
    }
  }
?>