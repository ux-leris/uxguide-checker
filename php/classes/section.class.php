<?php
    require_once("../dataAccess/sectionDAO.class.php");

    class Section
    {
        private $id;
        private $checklist_id;
        private $title;
        private $position;

        public function loadSection($conn, $id)
        {
            $sectionDAO = new SectionDAO;

            $result = $sectionDAO->select_section($conn, $id);

            $row = $result->fetch_assoc();

            $this->set_id($row["id"]);
            $this->set_checklist_id($row["checklist_id"]);
            $this->set_title($row["title"]);
            $this->set_position($row["position"]);
        }

        public function loadSectionItems($conn, $section_id)
        {
            $sectionDAO = new SectionDAO;

            return $sectionDAO->select_sectionItems($conn, $section_id);
        }

        public function set_id($id)
        {
            $this->id = $id;
        }

        public function set_checklist_id($checklist_id)
        {
            $this->checklist_id = $checklist_id;
        }

        public function set_title($title)
        {
            $this->title = $title;
        }

        public function set_position($position)
        {
            $this->position = $position;
        }

        public function get_id()
        {
            return $this->id;
        }

        public function get_checklist_id()
        {
            return $this->checklist_id;
        }

        public function get_title()
        {
            return $this->title;
        }

        public function get_position()
        {
            return $this->position;
        }
    }
?>