<?php
    require_once("../classes/database.class.php");
    require_once("../classes/checklist.class.php");
    require_once("../classes/section.class.php");

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

    $section = new Section;
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

        <div id="modal-share" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="../controllers/insert_access.php?c_id=<?= $checklist->get_id() ?>">
                        <div class="modal-header">
                            <h5>Share Checklist</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>E-mail</label>
                                <input type="email" name="email" id="emailAddress" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" onClick="sendChecklistResult()">
                                <span class="ml-1 mr-2">
                                    <i class="fas fa-user-plus"></i>
                                </span>
                                Share
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="container mt-5 mb-5">
            <h1><?= $checklist->get_title() ?></h1>
            <p class="lead text-muted text-justify"><?= $checklist->get_description() ?></p>
            <p>Created by <?= $checklist->get_authorName($conn) ?>.</p>
            <button type="button "class="btn btn-primary" data-toggle="modal" data-target="#modal-share">
                <span class="ml-1 mr-2">
                    <i class="fas fa-share-alt"></i>
                </span>
                Share Checklist
            </button>
            <hr>

            <?php
                $sectionResult = $checklist->loadSectionsOfChecklist($conn, $checklist->get_id());

                while($sectionRow = $sectionResult->fetch_assoc()) {
            ?>

            <div class="card mt-4 mb-4">
                <div class="card-header d-flex">
                    <div class="mr-auto">
                        <h3>Section <?= $sectionRow["position"] + 1 ?></h3>
                    </div>
                    <div class="ml-auto">
                        <a class="btn btn-secondary" href="sectionEditor.php?id=<?= $sectionRow["id"] ?>">
                            <span class="ml-1 mr-2">
                                <i class="fas fa-cog"></i>
                            </span>
                            Edit Section
                        </a>
                    </div>
                </div>
                <div class="card-body text-justify">
                    <?= $sectionRow["title"] ?>
                </div>
            </div>

            <?php
                }
            ?>

        </div>

		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="../../js/jquery-3.5.1.js"></script>
        <script src="../../js/popper-base.js"></script>
        <script src="../../js/bootstrap/bootstrap.js"></script>
	</body>
</html>