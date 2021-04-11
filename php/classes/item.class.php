<?php
    require_once("../dataAccess/checklistDAO.class.php");
    require_once("../dataAccess/itemDAO.class.php");

    class Item
    {
        private $id;
        private $checklist_id;
        private $section_id;
        private $text;
        private $link;
        private $position;

        public function loadItemLabels($conn, $checklist_id)
        {
            $checklistDAO = new ChecklistDAO;

            return $checklistDAO->select_checklistLabels($conn, $checklist_id);
        }

        public function loadItemAnswer($conn, $evaluation_id, $item_id)
        {
            $itemDAO = new ItemDAO;

            return $itemDAO->select_itemAnswer($conn, $evaluation_id, $item_id);
        }

        public function set_id($id)
        {
            $this->id = $id;
        }

        public function set_checklist_id($checklist_id)
        {
            $this->checklist_id = $checklist_id;
        }

        public function set_section_id($section_id)
        {
            $this->section_id = $section_id;
        }

        public function set_text($text)
        {
            $this->text = $text;
        }

        public function set_link($link)
        {
            $this->link = $link;
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

        public function get_section_id()
        {
            return $this->section_id;
        }

        public function get_text()
        {
            return $this->text;
        }

        public function get_link()
        {
            return $this->link;
        }

        public function get_position()
        {
            return $this->position;
        }
    }
?>