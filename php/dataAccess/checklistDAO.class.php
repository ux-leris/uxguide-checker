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
    }
?>