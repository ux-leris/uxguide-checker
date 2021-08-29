<?php
    require_once("../classes/database.class.php");
    require_once("../classes/checklist.class.php");
    require_once("../dataAccess/evaluationDAO.class.php");

    session_start();

	if(!isset($_SESSION["USER_ID"]))
	{
		header("location: ./login.php");
	}

    $db = new Database;
    $conn = $db->connect();

    $checklist_id = $_GET["c_id"];

    $checklist = new Checklist;
    $checklist->loadChecklist($conn, $checklist_id);

    if(!$checklist->get_id() || !$checklist->userHasAccess($conn, $_SESSION["USER_ID"])) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

    $evaluationDAO = new EvaluationDAO;

    $hasFinishedEval = false;
?>

<!doctype html>
<html lang="pt-BR">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="../../css/bootstrap/bootstrap.css">
		
		<!-- CSS Local -->
		<link rel="stylesheet" href="../../css/checklist.css">

        <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>

		<title><?= $checklist->get_title() ?> checklist</title>
	</head>

	<body>
		<!-- Navbar -->
		<?php include('../templates/navbar.php'); ?>

        <div class="container mt-5 mb-5">
            <div class="mb-3" style="display: flex; align-items: center;">
                <a href="../../index.php"><i class="fas fa-chevron-left fa-lg mr-3" style="color:#8FAD88;"></i></a>
                <h1><?= $checklist->get_title() ?></h1>
            </div>
            <p class="lead text-muted text-justify"><?= $checklist->get_description() ?></p>
            <p>Created by <?= $checklist->get_authorName($conn) ?>.</p>
            

            <?php
                if($checklist->get_author() == $_SESSION["USER_ID"]) {
            ?>            

            <?php 
                $evaluationResult = $evaluationDAO->select_evaluationsOfChecklist($conn, $checklist_id, $_SESSION["USER_ID"]);
                if($evaluationResult->num_rows > 0) { ?>
                    <a href="./checklistAnalytics.php?c_id=<?= $checklist->get_id() ?>" class="btn btn-primary" style="display: none;" id="analytics-btn">
                        <span class="ml-1 mr-2">
                            <i class="fas fa-chart-pie"></i>
                        </span>
                        Checklist Analytics
                    </a>
            <?php } ?>



            <hr>
            <h3>All Evaluations</h3>

            <div class="row mt-4 mb-4">

                <?php
                    while($evaluationRow = $evaluationResult->fetch_assoc()) {
                ?>

                <div class="col-md-6 mt-2 mb-2">
                    <div class="card shadow">
                        <div class="card-body">

                            <?php
                                if(!$evaluationRow["status"]) {
                            ?>

                            <span class="badge badge-pill badge-warning mt-2 mb-2">Pending</span>

                            <?php
                                } else {
                                    $hasFinishedEval = true;
                            ?>

                            <span class="badge badge-pill badge-success mt-2 mb-2">Done</span>

                            <?php
                                }
                            ?>

                            <h5>
                                Evaluation <?= $evaluationRow["id"] ?>
                            </h5>
                            <p>

                                <?php
                                    $date = date("d/m/Y", strtotime($evaluationRow["date"]));
                                    $time = date("H:i:s", strtotime($evaluationRow["date"]));
                                    $time_elapsed = $evaluationRow["time_elapsed"];
                                    $time_elapsed_converted = sprintf("%02d:%02d:%02d", floor($time_elapsed/3600), ($time_elapsed/60)%60, $time_elapsed%60);

                                    $evaluationAuthor = $evaluationDAO->select_authorName($conn, $evaluationRow["id"]);
                                    $author = $evaluationAuthor->fetch_row();
                                ?>

                                Evaluated by <?= $author[0] ?> in <?= $date ?> at <?= $time ?>.<br>
                                Time to evaluate: <?= $time_elapsed_converted ?>.
                            </p>
                            <div class="d-flex justify-content-start">

                                <?php
                                    if(!$evaluationRow["status"]) {                      
                                ?>

                                <a href="./checklist.php?id=<?= $checklist_id ?>&e_id=<?= $evaluationRow["id"] ?>" class="btn btn-primary">
                                    <span class="ml-1 mr-2">
                                        <i class="fas fa-play"></i>
                                    </span>
                                    Resume Evaluation
                                </a>

                                <?php
                                    } else {
                                ?>

                                <a href="./checklistResult.php?e_id=<?= $evaluationRow["id"] ?>" class="btn btn-primary">
                                    <span class="ml-1 mr-2">
                                        <i class="fas fa-poll-h"></i>
                                    </span>
                                    View Results
                                </a>

                                <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    }
                ?>

            </div>

            <?php
                } else {
            ?>

            <h3>Your Evaluations</h3>

            <div class="row mt-4 mb-4">

                <?php
                    $evaluationResult = $evaluationDAO->select_evaluationsOfUser($conn, $checklist_id, $_SESSION["USER_ID"]);

                    while($evaluationRow = $evaluationResult->fetch_assoc()) {
                ?>

                <div class="col-md-6 mt-2 mb-2">
                    <div class="card shadow">
                        <div class="card-body">

                            <?php
                                if(!$evaluationRow["status"]) {
                            ?>

                            <span class="badge badge-pill badge-warning mt-2 mb-2">Pending</span>

                            <?php
                                } else {
                                    $hasFinishedEval = true;
                            ?>

                            <span class="badge badge-pill badge-success mt-2 mb-2">Done</span>

                            <?php
                                }
                            ?>

                            <h5>
                                Evaluation <?= $evaluationRow["id"] ?>
                            </h5>
                            <p>

                                <?php
                                    $date = date("d/m/Y", strtotime($evaluationRow["date"]));
                                    $time = date("H:i:s", strtotime($evaluationRow["date"]));
                                    $time_elapsed = $evaluationRow["time_elapsed"];
                                    $time_elapsed_converted = sprintf("%02d:%02d:%02d", floor($time_elapsed/3600), ($time_elapsed/60)%60, $time_elapsed%60);

                                    $evaluationAuthor = $evaluationDAO->select_authorName($conn, $evaluationRow["id"]);
                                    $author = $evaluationAuthor->fetch_row();
                                ?>

                                Evaluated by <?= $author[0] ?> in <?= $date ?> at <?= $time ?>.<br>
                                Time to evaluate: <?= $time_elapsed_converted ?>.
                            </p>
                            <div class="d-flex justify-content-start">

                                <?php
                                    if(!$evaluationRow["status"]) {                      
                                ?>

                                <a href="./checklist.php?id=<?= $checklist_id ?>&e_id=<?= $evaluationRow["id"] ?>" class="btn btn-primary">
                                    <span class="ml-1 mr-2">
                                        <i class="fas fa-play"></i>
                                    </span>
                                    Resume Evaluation
                                </a>

                                <?php
                                    } else {
                                ?>

                                <a href="./checklistResult.php?e_id=<?= $evaluationRow["id"] ?>" class="btn btn-primary">
                                    <span class="ml-1 mr-2">
                                        <i class="fas fa-poll-h"></i>
                                    </span>
                                    View Results
                                </a>

                                <?php
                                    }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    }
                ?>

            <?php
                }
            ?>

            </div>
        </div>

		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="../../js/jquery-3.5.1.js"></script>
        <script src="../../js/popper-base.js"></script>
        <script src="../../js/bootstrap/bootstrap.js"></script>
	</body>
</html>

<script>
    $(document).ready(function() {
        hasFinishedEval = <?= $hasFinishedEval ?>;

        if(hasFinishedEval) {
            document.getElementById("analytics-btn").style.display = "inline-block";
        }
    });
</script>