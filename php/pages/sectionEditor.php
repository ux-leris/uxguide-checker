<?php
  require_once("../classes/database.class.php");
  require_once("../classes/checklist.class.php");
  require_once("../classes/section.class.php");

  session_start();

  if(!isset($_SESSION["USER_ID"])) {
    header("location: ./signIn.php");
  } else {
    $conn = Database::connect();

    $sectionId = $_GET["s_id"];

    $section = new Section($conn, $sectionId);

    if(!$section->getId()) {
      header("HTTP/1.0 401 Unauthorized");
      echo "<h1>401 Unauthorized</h1>";
      echo "You don't have permission to access this page.";
      exit();
    }

    $checklist = new Checklist($conn, $section->getChecklistId());

    if(!$checklist->getId() || $_SESSION["USER_ID"] != $checklist->getAuthorId()) {
      header("HTTP/1.0 401 Unauthorized");
      echo "<h1>401 Unauthorized</h1>";
      echo "You don't have permission to access this page.";
      exit();
    }
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
    <link rel="stylesheet" href="../../css/styles/sectionEditor.css">

    <title>Section Editor</title>
  </head>

  <body>
    <?php 
      require_once("../templates/modals/editItem.php"); 
      require_once("../templates/modals/deleteItemConfirmation.php"); 
    ?>
    
    <?php require_once("../templates/navbar.php"); ?>

    <div class="responseMessage"></div>

    <div class="container mt-5">
      <div class="checklist-infos">
        <a href="./checklistManager.php?c_id=<?= $checklist->getId() ?>">
          <i class="fas fa-chevron-left"></i>
        </a>
        <h1>Section <?= $section->getPosition() + 1 ?></h1>
      </div>
      <p class="lead text-justify"><?= htmlspecialchars($section->getTitle()) ?></p>

      <hr>

      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Information!</strong> You can add more items to your checklist using the form in end of page.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <h3 class="text-muted mb-3">Checklist Items</h3>

      <?php $sectionResult = $section->getSectionItems($conn, $section->getId()); ?>

      <?php if ($sectionResult->num_rows == 0) { ?>
        <div class="col-md-12 d-flex justify-content-center">
          <p class="text-muted">This checklist doesn't have any items yet.</p>
        </div>
      <?php } else { ?>
        <div id="checklistItems">
          <?php
            while($sectionRow = $sectionResult->fetch_assoc()) {
              require("../templates/sectionEditor/itemCard.php");
            }
          ?>
        </div>
      <?php } ?>

      <div class="card mt-2 mb-3">
        <div class="card-header">
          <h5>New Items</h5>
        </div>
        <div class="card-body">
          <form
            method="POST"
            action="../controllers/sectionEditor/insertItemsInChecklist.php?c_id=<?= $section->getChecklistId() ?>&s_id=<?= $section->getId() ?>"
          >
            <div id="itemInputs">
              <div class="row" id="itemInputGroup-1">
                <div class="form-group col-md-8">
                  <label>Item 1 - Title</label>
                  <input type="text" class="form-control" name="itemTitles[]">
                </div>
                <div class="form-group col-md-4">
                  <label>Reference Link</label>
                  <input type="url" class="form-control" name="referenceLinks[]">
                  <small class="text-muted">Optional Field</small>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-center">
              <button type="button" class="btn btn-info mr-2" onClick="itemsManager(lastNItemInputGroups - 1)">
                <span class="mr-2">
                  <i class="fas fa-minus"></i>
                </span>
                Item
              </button>
              <button type="button" class="btn btn-info ml-2" onClick="itemsManager(lastNItemInputGroups + 1)">
                <span class="mr-2">
                  <i class="fas fa-plus"></i>
                </span>
                Item
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="col-md-12 d-flex justify-content-center p-3">
        <button type="submit" class="btn btn-primary" onClick="submitForm()">
          <span class="ml-1 mr-2">
            <i class="fas fa-save"></i>
          </span>
          Save
        </button>
      </div>
    </div>
                    
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../../js/jquery-3.5.1.js"></script>
    <script src="../../js/popper-base.js"></script>
    <script src="../../js/bootstrap/bootstrap.js"></script>

    <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <script src="../../js/pages/sectionEditor/itemsManager.js"></script>
    <script src="../../js/pages/sectionEditor/editItem.js"></script>
    <script src="../../js/pages/sectionEditor/deleteItem.js"></script>
    <script src="../../js/pages/sectionEditor/dragAndDropItems.js"></script>
  </body>
</html>