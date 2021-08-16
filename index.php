<?php
    require_once("php/classes/database.class.php");
    require_once("php/dataAccess/checklistDAO.class.php");

	session_start();

	if(!isset($_SESSION["USER_ID"]))
	{
		header("location: ./php/pages/login.php");
	}

    $db = new Database;
    
    $conn = $db->connect();

    $checklistDAO = new ChecklistDAO;
?>

<!doctype html>
<html lang="pt-BR">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap/bootstrap.css">

		<!-- CSS Local -->
        <link rel="stylesheet" href="./css/index.css">

        <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>

		<title>Initial Page</title>
	</head>

	<body>
		<!-- Navbar -->
		<nav class="navbar navbar-expand-lg navbar-light shadow">
			<a class="navbar-brand text-light" href="#">
				UXTools
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNavDropdown">
			    <ul class="navbar-nav">
					<li class="nav-item dropdown">
						<a class="btn btn-primary dropdown-toggle" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Create
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
							<a class="dropdown-item" data-toggle="modal" data-target="#createChecklistForm" href="#">Checklist</a>
						<!--<a class="dropdown-item" href="#">Project</a>-->
						</div>
					</li>
				</ul>
				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class="nav-link text-light" href="./php/controllers/disconnect_user.php">
							Log out
						</a>						
					</li>
				</ul>
			</div>
		</nav>

		<div id="evaluationWarning" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Warning</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="close">&times;</button>
					</div>
					<div class="modal-body">
						<p>
							You're starting a new evaluation of this checklist.
							After <strong>continue</strong> a timer will be initialized to monitor the answer time.
						</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<a href="#" id="checklistEvaluation-link" class="btn btn-primary">Continue</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal criar checklist -->
		<div class="modal fade" id="createChecklistForm" tabindex="-1">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<form id="cadastrarChecklist" method="POST" action="php/controllers/insert_checklist.php">
						<div class="tab">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" arial-label="close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<h4 class="modal-title text-muted mt-2 mb-2">Step 1</h4>
								<div class="form-group">
									<label>Checklist Title</label>
									<input type="text" name="title" class="form-control">
								</div>
								<div class="form-group">
									<label>Checklist Description</label>
									<input type="text" name="description" class="form-control">
								</div>
							</div>
							<div class="modal-footer">
								<div>
									<button type="button" class="btn btn-primary" onclick="next()">Next</button>
								</div>
							</div>
						</div>
						<div class="tab">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" arial-label="close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<h4 class="modal-title text-muted mt-2 mb-2">Step 2</h4>
								<div class="form-group">
									<div class="row">
										<div class="col-md-12 d-flex align-items-center">
											<label class="mb-0">Set up your checklist sections</label>
										</div>
									</div>
									<hr>
									<div class="col-md-12" id="sectionTitleFields-area">
										<div id="sectionTitleField-1" class="form-group">
											<label>Section 1 - Title</label>
											<input type="text" name="sectionTitles[]" class="form-control">
										</div>
									</div>
									<div class="row">
										<div class="col-md-5 d-flex">
											<button type="button" class="btn btn-danger ml-auto" onclick="sectionTitlefieldsController(lastNSectionTitleFields - 1)">
												<span class="ml-2 mr-2">
													<i class="fas fa-minus-circle"></i>
												</span>
											</button>
										</div>
										<div class="col-md-2 d-flex align-items-center justify-content-center" id="qtdSections">1</div>
										<div class="col-md-5 d-flex">
											<button type="button" class="btn btn-success mr-auto" onclick="sectionTitlefieldsController(lastNSectionTitleFields + 1)">
												<span class="ml-2 mr-2">
													<i class="fas fa-plus-circle"></i>
												</span>
											</button>
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<div>
									<button type="button" class="btn btn-secondary" onclick="previous()">Previous</button>
									<button type="button" class="btn btn-primary" onclick="next()">Next</button>
								</div>
							</div>
						</div>
						<div class="tab">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" arial-label="close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<h4 class="modal-title text-muted mt-2 mb-2">Step 3</h4>
								<div class="form-group">
									<div class="row">
										<div class="col-md-12 d-flex align-items-center">
											<label class="mb-0">Set up your checklist answers options</label>
										</div>
									</div>
									<hr>
									<div class="col-md-12" id="itemLabelFields-area">
										<div class="form-group">
											<label>Option 1 - Text</label>
											<input type="text" name="itemLabels[]" class="form-control">											
										</div>
										<div class="form-group form-check ml-1">
											<input type="checkbox" name="hasJustification[]" class="form-check-input" value="0">
											<label>Need justification.</label>
										</div>
										<div class="form-group">
											<label>Option 2 - Text</label>
											<input type="text" name="itemLabels[]" class="form-control">											
										</div>
										<div class="form-group form-check ml-1">
											<input type="checkbox" name="hasJustification[]" class="form-check-input" value="1">
											<label>Need justification.</label>
										</div>
									</div>
									<div class="row">
										<div class="col-md-5 d-flex">
											<button type="button" class="btn btn-danger ml-auto" onclick="itemLabelFieldsController(lastNItemLabelFields - 1)">
												<span class="ml-2 mr-2">
													<i class="fas fa-minus-circle"></i>
												</span>
											</button>
										</div>
										<div class="col-md-2 d-flex align-items-center justify-content-center" id="qtdItemLabels">2</div>
										<div class="col-md-5 d-flex">
											<button type="button" class="btn btn-success mr-auto" onclick="itemLabelFieldsController(lastNItemLabelFields + 1)">
												<span class="ml-2 mr-2">
													<i class="fas fa-plus-circle"></i>
												</span>
											</button>
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<div>
									<button type="button" class="btn btn-secondary" onclick="previous()">Previous</button>
									<button type="button" class="btn btn-primary" onclick="submitForm()">Create</button>
								</div>
							</div>
						</div>
						<div class="col-md-12 text-center mt-3 mb-3">
							<span class="step"></span>
							<span class="step"></span>
							<span class="step"></span>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Seção minhas checklists -->
		<div class="container mt-5 mb-5">
            <h1>My checklists</h1>
            
            <?php
                $result = $checklistDAO->select_checklistsOfUser($conn, $_SESSION["USER_ID"]);

                if($result->num_rows > 0) {
			?>

			<div class="row mt-3">

				<?php
					while($row = $result->fetch_assoc()) {
				?>

				<div class="col-md-4">
					<div class="card mt-3 mb-3 shadow">
						<div class="card-body">
							<h5 class="text-justify"><?= $row["title"] ?></h5>
							<hr>
							<p class="text-justify"><?= $row["description"] ?></p>
							<div class="d-flex">
								<div class="mr-auto">
									<button class="btn btn-primary mr-1" onClick="setTempLink(<?= $row["id"] ?>)" <?= !boolval($row["published"]) ? "disabled" : null ?>>Evaluate Checklist</button>
									<a href="./php/pages/checklistEvaluations.php?c_id=<?= $row["id"] ?>" <?php 
									if(!boolval($row["published"])) { 
										echo 'class="btn btn-secondary ml-1 disabled"'; 
									} else { 
										echo 'class="btn btn-secondary ml-1"'; 
									} ?>>
										<span>
											<i class="fas fa-check-circle"></i>
										</span>
										Evaluations
									</a>
								</div>
								<div class="ml-auto">
									<!-- Icons -->
								</div>
							</div>
						</div>
						<div class="card-footer d-flex justify-content-center">
							<a href="./php/pages/checklistManager.php?c_id=<?= $row["id"] ?>" class="manage-link">
								<i class="fas fa-edit"></i>
								Manage
							</a>
						</div>
					</div>
				</div>

				<?php
					}
				?>

            </div>
            
            <?php
                }

                $result = $checklistDAO->select_checklistsSharedWithUser($conn, $_SESSION["USER_ID"]);

                if($result->num_rows > 0) {
            ?>

			<h1 class="mt-5">Shared With You</h1>

			<div class="row mt-3">

				<?php
					while($row = $result->fetch_assoc()) {
				?>

				<div class="col-md-4">
					<div class="card mt-3 mb-3 shadow">
						<div class="card-body">
							<h5><?= $row["title"] ?></h5>
							<p class="text-justify"><?= $row["description"] ?></p>
							<div class="d-flex">
								<div class="mr-auto">
									<a class="btn btn-primary mr-1" onClick="setTempLink(<?= $row["id"] ?>)">Evaluate Checklist</a>
									<a href="./php/pages/checklistEvaluations.php?c_id=<?= $row["id"] ?>" class="btn btn-secondary ml-1">
										<span>
											<i class="fas fa-check-circle"></i>
										</span>
										Evaluations
									</a>
								</div>
								<div class="ml-auto">
									<!-- Icons -->
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php
					}
				?>

            </div>
            
            <?php
                }
            ?>

        </div>

		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="js/jquery-3.5.1.js"></script>
        <script src="js/popper-base.js"></script>
        <script src="js/bootstrap/bootstrap.js"></script>

		<script type="text/javascript">

			var currentTab = 0;

			showTab(currentTab);

			function showTab(currentTab)
			{
				var tabs = document.getElementsByClassName("tab");

				tabs[currentTab].style.display = "block";

				setStepIndicator(currentTab);
			}

			function next()
			{
				if(!validateForm())
				{
					return false;
				}
				
				var tabs = document.getElementsByClassName("tab");

				tabs[currentTab].style.display = "none";

				currentTab += 1;

				showTab(currentTab);
			}

			function previous()
			{
				var tabs = document.getElementsByClassName("tab");

				tabs[currentTab].style.display = "none";

				currentTab -= 1;

				showTab(currentTab);
			}

			function validateForm()
			{
				var valid = true;

				var tabs = document.getElementsByClassName("tab");
				var inputs = tabs[currentTab].getElementsByTagName("input");

				for(var i = 0; i < inputs.length; i++)
				{
					if(inputs[i].value == "")
					{
						if(!(inputs[i].classList.contains("is-invalid")))
						{
							inputs[i].className += " is-invalid";
						}

						valid = false;
					}
					else
					{
						if(inputs[i].classList.contains("is-invalid"))
						{
							inputs[i].className = inputs[i].className.replace(" is-invalid", " is-valid");
						}
						else
						{
							if(!(inputs[i].classList.contains("is-valid")))
							{
								inputs[i].className += " is-valid";
							}
						}
					}
				}

				return valid;
			}

			function submitForm()
			{
				if(validateForm())
					$("#cadastrarChecklist").submit();
			}

			function setStepIndicator(currentTab)
			{
				var steps = document.getElementsByClassName("step");

				for(var i = 0; i < steps.length; i++)
				{
					steps[i].className = steps[i].className.replace("step active", "step");
				}

				steps[currentTab].className = "step active";
			}

		</script>

		<script type="text/javascript">

			var lastNSectionTitleFields = 1;

			function sectionTitlefieldsController(nFields)
			{
				if(nFields > lastNSectionTitleFields)
				{
					addSectionTitleFields(nFields);
				}
				else
				{
					if(nFields > 0)
					{
						delSectionTitleFields(nFields);
					}
				}
			}

			function addSectionTitleFields(nFields)
			{
				for(var i = lastNSectionTitleFields + 1; i <= nFields; i++)
				{
					var input = $("<div>", {
						"id": "sectionTitleField-" + i,
						"class": "form-group",
					}).append($("<label>", {
						"text": "Section" + " " + i + " - " + "Title",
					})).append($("<input>", {
						"type": "text",
						"name": "sectionTitles[]",
						"class": "form-control",
					}));

					$("#sectionTitleFields-area").append(input);
				}

				lastNSectionTitleFields = nFields;

				$("#qtdSections").text(lastNSectionTitleFields);
			}

			function delSectionTitleFields(nFields)
			{
				for(var i = (lastNSectionTitleFields); i > nFields; i--)
				{
					$("#sectionTitleField-" + i).remove();
				}

				lastNSectionTitleFields = nFields;

				$("#qtdSections").text(lastNSectionTitleFields);
			}

		</script>

		<script type="text/javascript">

			var lastNItemLabelFields = 2;

			function itemLabelFieldsController(nFields)
			{
				if(nFields > lastNItemLabelFields)
				{
					addItemLabelFields(nFields);
				}
				else
				{
					if(nFields > 1)
					{
						delItemLabelFields(nFields);
					}
				}
			}

			function addItemLabelFields(nFields)
			{
				for(var i = lastNItemLabelFields + 1; i <= nFields; i++)
				{
					var input = $("<div>", {
						"id": "itemLabelField-" + i,
						"class": "form-group",
					}).append($("<label>", {
						"text": "Option" + " " + i + " - " + "Text",
					})).append($("<input>", {
						"type": "text",
						"name": "itemLabels[]",
						"class": "form-control",
					}));

					var checkbox = $("<div>", {
						id: "justificationCheck-" + i,
						class: "form-group form-check ml-1"
					}).append($("<input>", {
						type: "checkbox",
						name: "hasJustification[]",
						class: "form-check-input",
						value: i - 1
					})).append($("<label>", {
						text: "Need justification."
					}));

					$("#itemLabelFields-area").append(input);
					$("#itemLabelFields-area").append(checkbox);
				}

				lastNItemLabelFields = nFields;

				$("#qtdItemLabels").text(lastNItemLabelFields);
			}

			function delItemLabelFields(nFields)
			{
				for(var i = lastNItemLabelFields; i > nFields; i--)
				{
					$("#itemLabelField-" + i).remove();
					$("#justificationCheck-" + i).remove();
				}

				lastNItemLabelFields = nFields;

				$("#qtdItemLabels").text(lastNItemLabelFields);
			}

		</script>

		<script>

			function setTempLink(c_id)
			{
				$("#checklistEvaluation-link").attr("href", "./php/pages/checklist.php?id=" + c_id);

				$("#evaluationWarning").modal("show");
			}

		</script>

	</body>
</html>