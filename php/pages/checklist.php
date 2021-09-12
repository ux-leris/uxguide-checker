<?php
  require_once("../classes/database.class.php");
  require_once("../classes/checklist.class.php");
  require_once("../classes/section.class.php");
  require_once("../classes/item.class.php");
  require_once("../classes/label.class.php");
  require_once("../classes/evaluation.class.php");

  session_start();

  if (!isset($_SESSION["USER_ID"])) {
    header("location: ./signIn.php");
  }

  $conn = Database::connect();

  $checklistId = $_GET["id"];

  $checklist = new Checklist($conn, $checklistId);

  $item = new Item;
  $label = new Label;

  if (isset($_GET["e_id"])) {
    $evaluationId = $_GET["e_id"];
    
    $evaluation = new Evaluation($conn, $evaluationId);
    
    if(!$checklist->getId() || !$evaluation->getId() || $evaluation->getAuthorId() != $_SESSION["USER_ID"] || !$checklist->userHasAccess($conn, $_SESSION["USER_ID"])) {
      header("HTTP/1.0 404 Not Found");
      echo "<h1>404 Not Found</h1>";
      echo "The page that you have requested could not be found.";
      exit();
    }

    $initialEvaluation = false;
  } else {
    if(!$checklist->getId() || !$checklist->userHasAccess($conn, $_SESSION["USER_ID"])) {
      header("HTTP/1.0 404 Not Found");
      echo "<h1>404 Not Found</h1>";
      echo "The page that you have requested could not be found.";
      exit();
    }

    $evaluationId = Evaluation::insertEvaluation($conn, $checklistId, $_SESSION["USER_ID"]);
    $initialEvaluation = true;
  }
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../css/bootstrap/bootstrap.css">

    <!-- CSS Local -->
    <link rel="stylesheet" href="../../css/styles/global.css">
    <link rel="stylesheet" href="../../css/styles/checklist.css">

    <title>Checklist Evaluation</title>
  </head>

  <body onLoad="startEvaluation(<?= isset($_GET["edit"]) ? "true" : "false" ?>, <?= $evaluationId; ?>, <?= $checklistId ?>)">
    <?php require_once("../templates/navbar.php"); ?>

    <div class="container mt-5 pb-5">
      <div class="checklist-infos">
        <a href="./checklistManager.php?c_id=<?= $checklist->getId() ?>">
          <i class="fas fa-chevron-left"></i>
        </a>
        <h1 class="text-justify"><?= $checklist->getTitle() ?></h1>
      </div>
      <p class="lead text-justify"><?= $checklist->getDescription() ?></p>
      <p class="text-muted">Created by <?= $checklist->getAuthorName($conn) ?>.</p>

      <hr>

      <?php if (isset($_GET["e_id"])) { ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
          <strong>Information!</strong> Your changes will be saved automatically.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php } ?>

      <form method="POST" action="../controllers/checklist/createEvaluation.php?e_id=<?= $evaluationId ?>">
        <div id="accordion">
          <?php $checklistResult = $checklist->getChecklistSections($conn, $checklist->getId()); ?>

          <?php while ($checklistRow = $checklistResult->fetch_assoc()) { ?>
            <div class="card mt-4 mb-4">
              <div class="card-header d-flex">
                <h3>Section <?= $checklistRow["position"] + 1 ?></h3>
              </div>
              <div class="card-body text-justify">
                <?= $checklistRow["title"] ?>
              </div>
              <div class="card-footer d-flex justify-content-center">
                <button type="button" id="toggleSection-<?= $checklistRow["id"] ?>" class="btn btn-primary" data-toggle="collapse" data-target="#section-<?= $checklistRow["id"] ?>">
                  <span id="toggleSection-icon" class="mr-2">
                    <i class="fas fa-chevron-down"></i>
                  </span>
                  Expand Section
                </button>
                </div>
              </div>

              <div class="col-md-12 collapse mt-3 mb-3" id="section-<?= $checklistRow["id"] ?>" data-parent="#accordion">

                <hr>

                <?php $sectionResult = Section::getSectionItems($conn, $checklistRow["id"]); ?>

                <?php while ($sectionRow = $sectionResult->fetch_assoc()) { ?>

                  <?php $answerResult = $item->loadItemAnswer($conn, $evaluationId, $sectionRow["id"]); ?>
                  <?php $answerRow = $answerResult->fetch_assoc(); ?>

                  <input type="hidden" name="id[]" value="<?= $sectionRow["id"] ?>">
                  <div class="card mt-2 mb-2" id="<?= $sectionRow["id"] ?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-2 d-flex align-items-center">
                          <select
                            class="form-control selectOption"
                            id="<?= "select-" . $sectionRow["id"] ?>"
                            name="label[]"
                            onChange="getHasJustify(this.id, this.value)" required>
                            
                            <?php $isLoadableOption = !$initialEvaluation && isset($answerRow["label"]); ?>
                            <?php $selectedOption = $isLoadableOption ? $answerRow["label"] : NULL; ?>

                            <?php if (!$isLoadableOption) { ?>
                              <option value="" selected disabled>Select</option>
                            <?php } ?>

                              <?php $optionResult = Item::getItemOptions($conn, $checklist->getId()); ?>

                              <?php while ($optionRow = $optionResult->fetch_assoc()) { ?> 
                                <?php $isSelected = $optionRow["id"] == $selectedOption; ?>
                                <option
                                  class="label" 
                                  value="<?= $optionRow["id"] ?>" 
                                  <?= $isSelected ? "selected" : "" ?>
                                >
                                  <?= $isSelected ? Label::getOptionTitle($conn, $selectedOption) : $optionRow["title"] ?>
                                </option>

                            <?php } ?>
                          </select>
                        </div>

                        <div class="col-md-10 d-flex align-items-center">
                          <?php if (!(isset($sectionRow["link"]))) { ?>
                            <?= $sectionRow["text"] ?>
                          <?php } else { ?>
                            <a href="<?= $sectionRow["link"] ?>" class="link" target="_blank"><?= $sectionRow["text"] ?></a>
                          <?php } ?>
                        </div>
                      </div>
                    </div>

                    <div class="card-footer collapse justificationArea" id="<?= "collapseJustify-" . $sectionRow["id"] ?>">
                      <div class="col-md-12">
                        <h5>Justify your selection:</h5>
                        <div class="col-md-12">
                          <?php $justification = isset($answerRow["justification"]) ? $answerRow["justification"] : NULL ?>
                          <input 
                            type="text"
                            class="form-control justificationInput"
                            id="<?= "inputJustify-" . $sectionRow["id"] ?>"
                            name="justification[]"
                            value="<?= $justification ?>"
                            placeholder="Add your justification"
                            required disabled
                          >
                        </div>
                      </div>
                    </div>
                  </div>
                <?php } ?>
            </div>
          <?php } ?>
        </div>

        <?php if (!isset($_GET["edit"])) { ?>
          <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-success">
              <span class="mr-2">
                <i class="fas fa-check"></i>
              </span>
              Submit Answers
            </button>
          </div>
        <?php } ?>

      </form>
    </div>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../../js/jquery-3.5.1.js"></script>
    <script src="../../js/popper-base.js"></script>
    <script src="../../js/bootstrap/bootstrap.js"></script>

    <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>

    <script src="../../js/pages/checklist/evaluationManager.js"></script>
    <script src="../../js/pages/checklist/evaluationTimeManager.js"></script>
    <script src="../../js/pages/checklist/evaluationEdit.js"></script>
  </body>
</html>

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
    var hasJustify = <?= $label->loadJustifiableLabels($conn, $checklist->getId()) ?>;

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