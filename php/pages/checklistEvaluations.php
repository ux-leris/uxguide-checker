<?php
    require_once("../classes/database.class.php");
    require_once("../classes/checklist.class.php");
    require_once("../classes/evaluation.class.php");
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

    $evaluation = new Evaluation;
    $evaluationDAO = new EvaluationDAO;
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
		<nav class="navbar navbar-expand-lg navbar-light bg-light shadow" id="navbar">
			<a class="navbar-brand text-light" href="#">
				UXTools
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-light" href="../../index.php">
                            Initial Page
                        </a>
                    </li>
                </ul>
			    <ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class="nav-link text-light" href="../controllers/disconnect_user.php">
							Log out
						</a>						
					</li>
				</ul>
			</div>
        </nav>

        <div class="container mt-5 mb-5">
            <h1><?= $checklist->get_title() ?></h1>
            <p class="lead text-muted text-justify"><?= $checklist->get_description() ?></p>
            <p>Created by <?= $checklist->get_authorName($conn) ?>.</p>
            <hr>

            <?php
                if($checklist->get_author() == $_SESSION["USER_ID"]) {
            ?>

            <h3>All Evaluations</h3>

            <div class="row mt-4 mb-4">

                <?php
                    $evaluationResult = $evaluationDAO->select_evaluationsOfChecklist($conn, $checklist_id, $_SESSION["USER_ID"]);

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
                                ?>

                                Evaluated by <?= $evaluation->get_authorName($conn, $evaluationRow["id"]) ?> in <?= $date ?> at <?= $time ?>.
                            </p>
                            <div class="d-flex justify-content-start">

                                <?php
                                    if(!$evaluationRow["status"]) {                      
                                ?>

                                <a href="./checklist.php?id=<?= $checklist_id ?>&e_id=<?= $evaluationRow["id"] ?>" class="btn btn-primary">
                                    <span class="ml-1 mr-2">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    Finish Evaluation
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
                                ?>

                                Evaluated by <?= $evaluation->get_authorName($conn, $evaluationRow["id"]) ?> in <?= $date ?> at <?= $time ?>.
                            </p>
                            <div class="d-flex justify-content-start">

                                <?php
                                    if(!$evaluationRow["status"]) {                      
                                ?>

                                <a href="./checklist.php?id=<?= $checklist_id ?>&e_id=<?= $evaluationRow["id"] ?>" class="btn btn-primary">
                                    <span class="ml-1 mr-2">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    Finish Evaluation
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