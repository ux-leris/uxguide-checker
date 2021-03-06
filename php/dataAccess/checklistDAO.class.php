<?php
  class ChecklistDAO
  {
    public static function insertChecklist($conn, $title, $description, $authorId)
    {
      $query = "INSERT INTO checklist(title, description, author_id) values(?, ?, ?)";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("sss", $title, $description, $authorId);
      
      $stmt->execute();

      return $stmt->insert_id;
    }

    public static function getJustifiableOptions($conn, $checklistId)
    {
      $query = "SELECT id FROM label WHERE checklist_id = ? AND hasJustification = true";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $checklistId);

      $stmt->execute();

      return $stmt->get_result();
    }

    public static function insertSection($conn, $checklistId, $title, $position)
    {
      $query = "INSERT INTO section(checklist_id, title, position) values(?, ?, ?)";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("sss", $checklistId, $title, $position);

      return $stmt->execute();
    }

    public static function shareChecklist($conn, $email, $checklistId)
    {
      $query = "SELECT * FROM user WHERE email = ?";

      $stmt = $conn->prepare($query);

      $stmt->bind_param("s", $email);
      $stmt->execute();

      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      $query = "INSERT INTO access(user_id, checklist_id) VALUES(?, ?)";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("ss", $row["id"], $checklistId);

      return $stmt->execute();
    }

    public static function getChecklistInfos($conn, $checklistId)
    {
      $query = "SELECT * FROM checklist WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $checklistId);

      $stmt->execute();

      return $stmt->get_result();
    }

    public static function getChecklistSections($conn, $checklistId)
    {
      $query = "SELECT * FROM section WHERE checklist_id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $checklistId);

      $stmt->execute();

      return $stmt->get_result();
    }

    public static function getChecklistItems($conn, $checklistId)
    {
      $query = "SELECT * FROM checklist_item WHERE checklist_id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $checklistId);

      $stmt->execute();

      return $stmt->get_result();
    }

    public static function getItemOptions($conn, $checklistId)
    {
      $query = "SELECT * FROM label WHERE checklist_id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $checklistId);
      
      $stmt->execute();

      return $stmt->get_result();
    }

    public static function getUserChecklists($conn, $userId)
    {
      $query = "SELECT * FROM checklist WHERE author_id = ? ORDER BY id DESC";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $userId);

      $stmt->execute();

      return $stmt->get_result();
    }

    public static function getSharedChecklists($conn, $userId)
    {
        $query = "SELECT * FROM access, checklist WHERE user_id = ? AND checklist_id = id ORDER BY id DESC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $userId);

        $stmt->execute();

        return $stmt->get_result();
    }

      public function select_authorName($conn, $author_id)
      {
          $query = "select * from user where id = ?";

          $stmt = $conn->prepare($query);

          $stmt->bind_param("s", $author_id);
          $stmt->execute();

          return $stmt->get_result();
      }

      public function select_checklistEvaluations($conn, $checklist_id) {
          $query = "select * from evaluation where checklist_id = ?";

          $stmt = $conn->prepare($query);

          $stmt->bind_param("s", $checklist_id);
          $stmt->execute();

          return $stmt->get_result();
      }

    public static function verifyAccess($conn, $userId, $checklistId)
    {
        $query = "SELECT user_id FROM access WHERE user_id = ? AND checklist_id = ?"; 

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $userId, $checklistId);

        $stmt->execute();
        
        $stmt->store_result();

        return $stmt->num_rows;
    }

    public static function countChecklistItems($conn, $checklistId) {
      $query = "SELECT COUNT(*) AS count FROM checklist_item WHERE checklist_id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $checklistId);
      
      $stmt->execute();

      return $stmt->get_result();
    }

    public static function publish($conn, $checklistId) {
      $query = "UPDATE checklist SET published = 1 WHERE id = ?";

      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $checklistId);

      return $stmt->execute();
    }

      public function getNumberOfAnswersBySections($conn, $checklist_id) {
          $query = "
          -- Selecting answers quantity by section and label
          SELECT z.section_id as section, z.label_id as label, count(w.label) as qty_answers 
          FROM 
              -- Selecting all answers from sections including options that wasn't used (to return count 0 in the answers count)
              (SELECT * 
                  FROM 
                      -- Selecting all checklist labels(answers options)
                      (SELECT id as label_id 
                  FROM label 
                  WHERE checklist_id=?)y 
                      CROSS JOIN 
                          -- Selecting all checklist sections
                          (SELECT id as section_id 
                      FROM section 
                      WHERE checklist_id=?)x)z 
              LEFT OUTER JOIN 
                  -- Selecting all answers from sections
                  (SELECT x.section_id as section_id, label 
                  FROM checklist_item_data 
                  JOIN 
                          -- Selecting all items from checklist
                      (SELECT section_id, id as item_id 
                      FROM checklist_item 
                      WHERE section_id 
                      IN 
                              -- Selecting all sections from checklist
                          (SELECT id as section_id 
                          FROM `section` WHERE checklist_id = ?))x 
                  ON checklist_item_id=x.item_id 
                  WHERE label IS NOT NULL)w 
          ON z.label_id=w.label 
          AND z.section_id=w.section_id 
          GROUP BY z.section_id, z.label_id";

          $stmt = $conn->prepare($query);

          $stmt->bind_param("sss", $checklist_id, $checklist_id, $checklist_id);
          $stmt->execute();

          return $stmt->get_result();
      }

      public function getNumberOfAnswersByQuestions($conn, $checklist_id, $section_id) {
          $query = "SELECT item_id, text, label_id, count(label) as count FROM (SELECT * FROM (SELECT id as item_id, text FROM checklist_item WHERE checklist_id = ? AND section_id = ? ORDER BY item_order)x JOIN (SELECT id as label_id FROM label WHERE checklist_id = ?)y)z LEFT OUTER JOIN checklist_item_data ON item_id = checklist_item_id AND label_id = label GROUP BY item_id, label_id";

          $stmt = $conn->prepare($query);

          $stmt->bind_param("sss", $checklist_id, $section_id, $checklist_id);
          $stmt->execute();

          return $stmt->get_result();
      }

  }
?>