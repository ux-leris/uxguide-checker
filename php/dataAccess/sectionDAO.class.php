<?php
    class SectionDAO
    {
        public function select_section($conn, $section_id)
        {
            $query = "select * from section where id = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("s", $section_id);
            $stmt->execute();

            return $stmt->get_result();
        }

        public function select_sectionItems($conn, $section_id)
        {
            $query = "select * from checklist_item where section_id = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("s", $section_id);
            $stmt->execute();

            return $stmt->get_result();
        }
    }
?>