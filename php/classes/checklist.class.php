<?php
    require_once("../dataAccess/checklistDAO.class.php");

    class Checklist
    {
        private $id;
        private $title;
        private $description;
        private $author;

        function loadChecklist($conn, $checklist_id)
        {
            $checklistDAO = new ChecklistDAO;

            $result = $checklistDAO->select_checklist($conn, $checklist_id);

            $row = $result->fetch_assoc();

            if($row) {
                $this->id = $row["id"];
                $this->title = $row["title"];
                $this->description = $row["description"];
                $this->author = $row["author_id"];
            } else {
                $this->id = NULL;
            }
        }

        function loadSectionsOfChecklist($conn, $checklist_id)
        {
            $checklistDAO = new ChecklistDAO;

            return $checklistDAO->select_sectionsOfChecklist($conn, $checklist_id);
        }

        public function set_id($id)
        {
            $this->id = $id;
        }

        public function set_title($title)
        {
            $this->title = $title;
        }

        public function set_description($description)
        {
            $this->description = $description;
        }

        public function set_author($author)
        {
            $this->author = $author;
        }

        public function get_id()
        {
            return $this->id;
        }

        public function get_title()
        {
            return $this->title;
        }

        public function get_description()
        {
            return $this->description;
        }

        public function get_author()
        {
            return $this->author;
        }

        public function get_authorName($conn)
        {
            $checklistDAO = new ChecklistDAO;

            $result = $checklistDAO->select_authorName($conn, $this->author);
            $row = $result->fetch_assoc();

            return $row["name"];
        }

        public function userHasAccess($conn, $user_id) {
            $checklistDAO = new ChecklistDAO;

            $result = $checklistDAO->verifyAccess($conn, $user_id, $this->id);
            $row = $result->fetch_assoc();

            if(!$row) {
                return false;
            } else {
                return true;
            }
        }
    }
?>