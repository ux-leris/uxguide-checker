<?php
    class ChecklistDAO
    {
        public function insert_checklist($conn, $title, $description, $author_id)
        {
            $query = "insert into checklist(title, description, author_id) values('".$title."', '".$description."', ".$author_id.")";

            $stmt = $conn->prepare($query);

            $stmt->execute();

            return $stmt->insert_id;
        }

        public function insert_section($conn, $checklistId, $title, $pos)
        {
            $query = "insert into section(checklist_id, title, position) values(".$checklistId.", '".$title."', ".$pos.")";

            $stmt = $conn->prepare($query);

            $stmt->execute();
        }

        public function insert_access($conn, $email, $checklist_id)
        {
            $query = "select * from user where email = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("s", $email);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $query = "insert into access(user_id, checklist_id) values(".$row["id"].", ".$checklist_id.")";

            $stmt = $conn->prepare($query);

            $stmt->execute();
        }

        public function select_checklist($conn, $checklist_id)
        {
            $query = "select * from checklist where id = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("s", $checklist_id);
            $stmt->execute();

            return $stmt->get_result();
        }

        public function select_sectionsOfChecklist($conn, $checklist_id)
        {
            $query = "select * from section where checklist_id = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("s", $checklist_id);
            $stmt->execute();

            return $stmt->get_result();
        }

        public function select_checklistLabels($conn, $checklist_id)
        {
            $query = "select * from label where checklist_id = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("s", $checklist_id);
            $stmt->execute();

            return $stmt->get_result();
        }

        public function select_checklistsOfUser($conn, $user_id)
        {
            $query = "select * from checklist where author_id = ? order by id desc";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("s", $user_id);
            $stmt->execute();

            return $stmt->get_result();
        }

        public function select_checklistsSharedWithUser($conn, $user_id)
        {
            $query = "select * from access, checklist where user_id = ? and checklist_id = id order by id desc";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("s", $user_id);
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

        public function verifyAccess($conn, $user_id, $checklist_id) {
            $query = "select user_id from access where user_id = ? and checklist_id = ? limit 1";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("ss", $user_id, $checklist_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            return $row;
        }

        public function countItems($conn, $checklist_id) {
            $query = "select count(*) as count from checklist_item where checklist_id= ?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $checklist_id);
            $stmt->execute();

            return $stmt->get_result();
        }

        public function publish($conn, $checklist_id) {
            $query = "update checklist set published=1 where id=?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $checklist_id);
            $stmt->execute();

            return $stmt->affected_rows;
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
            $query = "SELECT item_id, text, label, count(label) as count FROM (SELECT id as item_id, text FROM checklist_item WHERE checklist_id = ? AND section_id = ? ORDER BY item_order)x JOIN checklist_item_data ON item_id = checklist_item_id GROUP BY item_id, label";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("ss", $checklist_id, $section_id);
            $stmt->execute();

            return $stmt->get_result();
        }

    }
?>