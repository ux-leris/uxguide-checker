<?php
    class userDAO
    {
        public function insert_user($conn, $name, $email, $password)
        {
            $query = "insert into user(name, email, password) values('".$name."', '".$email."', '".$password."')";

            $stmt = $conn->prepare($query);

            $stmt->execute();
        }

        public function authenticate_user($conn, $email, $password)
        {
            $query = "select * from user where email = ? and password = ?";

            $stmt = $conn->prepare($query);

            $stmt->bind_param("ss", $email, $password);

            if($stmt->execute())
            {
                $stmt->store_result();

                if($stmt->affected_rows < 1)
                {
                    header("location: ../pages/login.php");
                }
                else
                {
                    $stmt->bind_result($id, $name, $email, $password);
                    $stmt->fetch();

                    session_start();

                    $_SESSION["USER_ID"] = $id;

                    header("location: ../../index.php");
                }
            }
            else
            {
                header("location: ../pages/login.php");
            }
        }
    }
?>