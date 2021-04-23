<?php
    require_once("../classes/database.class.php");

    class EvaluationDAO
    {
        public function insert($conn, $checklist_id, $author_id)
        {
            $query = "INSERT INTO evaluation(checklist_id, author, status) VALUES(".$checklist_id.", ".$author_id.", FALSE)";

            $stmt = $conn->prepare($query);

            $stmt->execute();

            return $stmt->insert_id;
        }

        public function insert_evaluation($conn, $checklist_id, $author)
        {
            $query = "INSERT INTO evaluation(checklist_id, author) VALUES(".$checklist_id.", '".$author."')";

            $stmt = $conn->prepare($query);

            $stmt->execute();

            return $stmt->insert_id;
        }

        public function insert_pause($conn, $evaluation_id, $time)
        {
            $query = "INSERT INTO pause(evaluation_id, time) VALUES(".$evaluation_id.", ".$time.")";

            $stmt = $conn->prepare($query);

            $stmt->execute();
        }

        public function update_evaluation($conn, $evaluation_id)
        {
            $query = "UPDATE evaluation SET status = true WHERE id = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("s", $evaluation_id);
            $stmt->execute();
        }

        public function select_evaluation($conn, $evaluation_id)
        {
            $query = "select * from evaluation where id = ".$evaluation_id;

            $stmt = $conn->prepare($query);

            $stmt->execute();

            return $stmt->get_result();
        }

        public function select_evaluationsOfChecklist($conn, $checklist_id, $user_id)
        {
            $query = "select * from evaluation where checklist_id = ? and (status = true or author = ?)";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("ss", $checklist_id, $user_id);
            $stmt->execute();

            return $stmt->get_result();
        }

        public function select_evaluationsOfUser($conn, $checklist_id, $user_id)
        {
            $query = "select * from evaluation where checklist_id = ? and author = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("ss", $checklist_id, $user_id);
            $stmt->execute();

            return $stmt->get_result();
        }

        public function select_authorName($conn, $evaluation_id)
        {
            $query = "select u.name from evaluation as e, user as u where e.id = ? and author = u.id";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("s", $evaluation_id);
            $stmt->execute();

            return $stmt->get_result();
        }
    }
?>