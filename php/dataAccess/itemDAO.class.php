<?php
    class ItemDAO
    {
        public function insert_item($conn, $checklist_id, $section_id, $itemText, $link)
        {
            if(isset($link))
                $query = "insert into checklist_item(checklist_id, section_id, text, link) values(".$checklist_id.", ".$section_id.", '".$itemText."', '".$link."')";
            else
                $query = "insert into checklist_item(checklist_id, section_id, text) values(".$checklist_id.", ".$section_id.", '".$itemText."')";

            $stmt = $conn->prepare($query);

            $stmt->execute();
        }

        public function insert($conn, $evaluation_id, $checklist_id)
        {
            $query = "SELECT * FROM checklist_item WHERE checklist_id = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("s", $checklist_id);
            $stmt->execute();

            $result = $stmt->get_result();

            while($row = $result->fetch_assoc())
            {
                $query = "INSERT INTO checklist_item_data(evaluation_id, checklist_item_id) VALUES(".$evaluation_id.", ".$row["id"].")";

                $stmt = $conn->prepare($query);

                $stmt->execute();
            }
        }

        public function insert_itemAnswer($conn, $evaluation_id, $item_id, $label, $justification, $dueDate)
        {
            $query = "insert into checklist_item_data(evaluation_id, checklist_item_id, label, justification, dueDate) values(".$evaluation_id.", ".$item_id.", ".$label.", ".$justification.", ".$dueDate.")";

            $stmt = $conn->prepare($query);

            $stmt->execute();
        }

        public function update_item($conn, $item_id, $text, $link)
        {
            $query = "update checklist_item set text = ?, link = ? where id = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("sss", $text, $link, $item_id);

            return $stmt->execute();
        }

        public function update_itemLabelAnswer($conn, $evaluation_id, $item_id, $label_id)
        {
            $query = "UPDATE checklist_item_data SET label = ? WHERE evaluation_id = ? AND checklist_item_id = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("sss", $label_id, $evaluation_id, $item_id);
            $stmt->execute();
        }

        public function update_itemJustificationAnswer($conn, $evaluation_id, $item_id, $justification)
        {
            $query = "UPDATE checklist_item_data SET justification = ? WHERE evaluation_id = ? AND checklist_item_id = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("sss", $justification, $evaluation_id, $item_id);
            $stmt->execute();
        }

        public function delete_item($conn, $item_id)
        {
            $query = "delete from checklist_item where id = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("s", $item_id);

            return $stmt->execute();
        }

        public function select_itemAnswer($conn, $evaluation_id, $item_id)
        {
            $query = "select * from checklist_item_data where evaluation_id = ".$evaluation_id." and checklist_item_id = ".$item_id;

            $stmt = $conn->prepare($query);

            $stmt->execute();

            return $stmt->get_result();
        }
    }
?>