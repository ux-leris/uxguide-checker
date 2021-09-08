<?php
require_once("../classes/database.class.php");
require_once("../classes/checklist.class.php");
require_once("../classes/section.class.php");
require_once("../classes/item.class.php");
require_once("../classes/label.class.php");
require_once("../classes/evaluation.class.php");

session_start();

if (!isset($_SESSION["USER_ID"])) {
	header("location: ./login.php");
}

$db = new Database;
$conn = $db->connect();

$checklist_id = $_GET["id"];

$checklist = new Checklist;
$checklist->loadChecklist($conn, $checklist_id);

$section = new Section;
$item = new Item;
$label = new Label;
$evaluation = new Evaluation;

if (isset($_GET["e_id"])) {

	$evaluation_id = $_GET["e_id"];
	$evaluation->loadEvaluation($conn, $evaluation_id);
	
	if(!$checklist->get_id() || !$evaluation->get_id() || $evaluation->get_author() != $_SESSION["USER_ID"] || !$checklist->userHasAccess($conn, $_SESSION["USER_ID"])) {
		header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
	}

	$initialEvaluation = false;
} else {

	if(!$checklist->get_id() || !$checklist->userHasAccess($conn, $_SESSION["USER_ID"])) {
		header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
	}

	$evaluation_id = $evaluation->insert_evaluation($conn, $checklist_id, $_SESSION["USER_ID"]);

	$initialEvaluation = true;
}
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

<body onLoad="start()">
	<!-- Navbar -->
	<?php include('../templates/navbar.php'); ?>

	<div class="container mt-5 mb-5">
		<div class="mb-3" style="display: flex; align-items: center;">
			<a href="./checklistEvaluations.php?c_id=<?= $checklist->get_id() ?>" style="color:#8FAD88;"><i class="fas fa-chevron-left fa-lg mr-3"></i></a>
			<h1><?= $checklist->get_title() ?></h1>
		</div>
		<p class="lead text-muted text-justify"><?= $checklist->get_description() ?></p>
		<p>Created by <?= $checklist->get_authorName($conn) ?>.</p>
		<hr>
		<?php if(isset($_GET["edit"])) { ?>
			<p><b>Info:</b> The changes will be saved automatically ✅</p>
		<?php } ?>
		<form class="col-md-12" method="POST" action="../controllers/insert_answers.php?c_id=<?= $checklist->get_id() ?>&e_id=<?= $evaluation_id ?>">
			<div class="row">
				<div class="col-md-12" id="accordion">

					<?php
					$sectionResult = $checklist->loadSectionsOfChecklist($conn, $checklist->get_id());

					while ($sectionRow = $sectionResult->fetch_assoc()) {
					?>

						<div class="card mt-4 mb-4">
							<div class="card-header d-flex">
								<div class="mr-auto">
									<h3>Section <?= $sectionRow["position"] + 1 ?></h3>
								</div>
							</div>
							<div class="card-body text-justify">
								<?= $sectionRow["title"] ?>
							</div>
							<div class="card-footer d-flex justify-content-center">
								<button type="button" id="toggleSection-<?= $sectionRow["id"] ?>" class="btn btn-primary" data-toggle="collapse" data-target="#section-<?= $sectionRow["id"] ?>">
									<span id="toggleSection-icon" class="ml-1 mr-2">
										<i class="fas fa-chevron-down"></i>
									</span>
									Expand Section
								</button>
							</div>
						</div>
						<div class="col-md-12 collapse mt-3 mb-3" id="section-<?= $sectionRow["id"] ?>" data-parent="#accordion">
							<hr>

							<?php
							$itemResult = $section->loadSectionItems($conn, $sectionRow["id"]);

							while ($itemRow = $itemResult->fetch_assoc()) {
								$answerResult = $item->loadItemAnswer($conn, $evaluation_id, $itemRow["id"]);
								$answerRow = $answerResult->fetch_assoc();
							?>

								<input type="hidden" name="id[]" value="<?= $itemRow["id"] ?>">
								<div class="card mt-2 mb-2" id="<?= $itemRow["id"] ?>">
									<div class="card-body">
										<div class="row">
											<div class="col-md-2 d-flex align-items-center">
												<select class="form-control selectInput" id="<?= "select-" . $itemRow["id"] ?>" name="label[]" onChange="getHasJustify(this.id, this.value)" required>

													<?php
													if (!($initialEvaluation)) {
														if (isset($answerRow["label"]))
															$selectedLabel = $answerRow["label"];
														else {
															$selectedLabel = NULL;
													?>

															<option value="" selected disabled>Select</option>

														<?php
														}
													} else {
														$selectedLabel = NULL;
														?>

														<option value="" selected disabled>Select</option>

														<?php
													}

													$labelResult = $item->loadItemLabels($conn, $checklist->get_id());

													while ($labelRow = $labelResult->fetch_assoc()) {
														if ($labelRow["id"] == $selectedLabel) {
														?>

															<option class="label" value="<?= $labelRow["id"] ?>" selected><?= $label->loadLabelTitle($conn, $selectedLabel) ?></option>

														<?php
														} else {
														?>

															<option class="label" value="<?= $labelRow["id"] ?>" <?php ?>><?= $labelRow["title"] ?></option>

													<?php
														}
													}
													?>

												</select>
											</div>
											<div class="col-md-10 d-flex align-items-center">

												<?php
												if (!(isset($itemRow["link"]))) {
												?>

													<?= $itemRow["text"] ?>

												<?php
												} else {
												?>

													<a href="<?= $itemRow["link"] ?>" class="link" target="_blank"><?= $itemRow["text"] ?></a>

												<?php
												}
												?>

											</div>
										</div>
									</div>
									<div class="card-footer collapse justificationArea" id="<?= "collapseJustify-" . $itemRow["id"] ?>">
										<div class="col-md-12">
											<h5>Justify your selection:</h5>
											<div class="row">
												<div class="col-md-12">

													<?php
													if (isset($answerRow["justification"]))
														$val = $answerRow["justification"];
													else
														$val = NULL;
													?>

													<input type="text" class="form-control justificationInput" id="<?= "inputJustify-" . $itemRow["id"] ?>" name="justification[]" value="<?= $val ?>" placeholder="Add your justification" required disabled>
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
			</div>
			<?php if(!isset($_GET["edit"])) { ?>
				<div class="d-flex justify-content-center mt-3">
					<button type="submit" class="btn btn-success ml-2">
						<span class="ml-1 mr-2">
							<i class="fas fa-check"></i>
						</span>
						Submit Answers
					</button>
				</div>
			<?php } ?>
		</form>
	</div>

	<!-- Optional JavaScript -->
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="../../js/jquery-3.5.1.js"></script>
	<script src="../../js/popper-base.js"></script>
	<script src="../../js/bootstrap/bootstrap.js"></script>

	<script type="text/javascript">
		var lastBtn = null;
		var lastIcon = null;

		var btnsExpand = document.getElementsByClassName("btn btn-primary");

		for (var i = 0; i < btnsExpand.length; i++) {
			btnsExpand[i].addEventListener("click", function() {

				var thisIcon = $('#' + this.id).find("i");

				if (this.hasAttribute("aria-expanded")) {
					if (this.getAttribute("aria-expanded") == "true") {
						this.lastChild.data = "Expand Section";
						thisIcon.attr("class", "fas fa-chevron-down");
					} else {
						this.lastChild.data = "Collapse Section";
						thisIcon.attr("class", "fas fa-chevron-up");

						if (this != lastBtn) {
							lastBtn.lastChild.data = "Expand Section";
							lastIcon.attr("class", "fas fa-chevron-down");
						}
					}
				} else {
					this.lastChild.data = "Collapse Section";
					thisIcon.attr("class", "fas fa-chevron-up");

					if (lastBtn != null && this != lastBtn) {
						lastBtn.lastChild.data = "Expand Section";
						lastIcon.attr("class", "fas fa-chevron-down");
					}
				}

				lastBtn = this;
				lastIcon = $('#' + this.id).find("i");
			});
		}
	</script>

	<!-- Controlador de justificativa -->
	<script type="text/javascript">
		var hasJustify = <?= $label->loadJustifiableLabels($conn, $checklist->get_id()) ?>;

		var select;
		var label;

		var find;

		function getHasJustify(sId, lId) {
			find = false;

			var select = sId;
			var label = lId;

			var item = document.getElementById(select).parentNode.parentNode.parentNode.parentNode.id;

			for (var i = 0; i < hasJustify.length; i++) {
				if (hasJustify[i] == lId) {
          $('#inputJustify-' + item).prop("value", "");
					$('#collapseJustify-' + item).collapse('show');
					$('#inputJustify-' + item).prop("disabled", false);

					find = true;

					break;
				}
			}

			if (!find) {
				$('#collapseJustify-' + item).collapse('hide');
        $('#inputJustify-' + item).prop("disabled", true);
        $('#inputJustify-' + item).prop("value", "").trigger("change");
			}
		}
	</script>

	<script type="text/javascript">
		function filterId(id) {
			return id.substr(id.indexOf("-") + 1);
		}
	</script>

	<script type="text/javascript">

		$('.selectInput').on('change', function() {

			$.ajax({
					type: "POST",
					url: "../controllers/update_answers.php",
					data: {
						evaluation_id: <?= $evaluation_id ?>,
						item_id: filterId(this.id),
						label_id: this.value,
					}
				});
		});

		$('.justificationInput').on('change', function() {
				$.ajax({
						type: "POST",
						url: "../controllers/update_answers.php",
						data: {
								evaluation_id: <?= $evaluation_id ?>,
								item_id: filterId(this.id),
								justification: this.value,
						},
				});
		});

	</script>

	<script>
		var seconds = 0;
		var editing = <?=	isset($_GET["edit"]) ? 1 : 0 ?>

		function startChronometer() {
			var timer = setInterval(() => {
				seconds++;
			}, 1000);
		}

		if(!editing) {
			document.addEventListener('visibilitychange', function saveTime() {
				if (document.visibilityState === 'hidden') {
					var data = new FormData();
					data.append('e_id', <?= $evaluation_id ?>);
					data.append('seconds', seconds);
					navigator.sendBeacon("../controllers/update_time.php", data); 
				}
			});
		}

		function start() {
			
			if(!editing) {
				startChronometer();
			}
			<?php
			if (!($initialEvaluation)) {
			?>

				renderCollapsedAreas();

			<?php
			}
			?>
		}
	</script>

	<script type="text/javascript">
		function renderCollapsedAreas() {
			var hasJustify = <?= $label->loadJustifiableLabels($conn, $checklist->get_id()) ?>;
			var justificationAreas = document.getElementsByClassName("justificationArea");

			for (var i = 0; i < justificationAreas.length; i++) {
				var itemId = filterId(justificationAreas[i].id);

				for (var j = 0; j < hasJustify.length; j++) {
					if (hasJustify[j] == $("#select-" + itemId).val()) {
						$('#collapseJustify-' + itemId).collapse('show');
						$('#inputJustify-' + itemId).prop("disabled", false);

						break;
					}
				}
			}
		}
	</script>

</body>

</html>