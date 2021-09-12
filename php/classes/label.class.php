<?php
  require_once("../dataAccess/labelDAO.class.php");

  class Label
  {
      public function loadJustifiableLabels($conn, $checklist_id)
      {
          $justifiableLabels = array();
          $labelDAO = new LabelDAO;

          $result = $labelDAO->select_justifiableLabels($conn, $checklist_id);

          while($row = $result->fetch_assoc())
              array_push($justifiableLabels, $row["id"]);

          return json_encode($justifiableLabels);
      }

    public static function getOptionTitle($conn, $labelId)
    {
      return labelDAO::getOptionTitle($conn, $labelId);
    }
  }
?>