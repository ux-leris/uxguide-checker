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

            $this->set_id($row["id"]);
            $this->set_title($row["title"]);
            $this->set_description($row["description"]);
            $this->set_author($row["author_id"]);
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
    }
?>