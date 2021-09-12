<?php
  require_once("../classes/database.class.php");
  require_once("../classes/checklist.class.php");
  require_once("../classes/section.class.php");

  session_start();

  if (!isset($_SESSION["USER_ID"])) {
    header("location: ./signIn.php");
  } else {
    $conn = Database::connect();

    $checklistId = $_GET["c_id"];

    $checklist = new Checklist($conn, $checklistId);

    if(!$checklist->getId() || $_SESSION["USER_ID"] != $checklist->getAuthorId()) {
      header("HTTP/1.0 404 Not Found");
      echo "<h1>404 Not Found</h1>";
      echo "The page that you have requested could not be found.";
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
    <link rel="stylesheet" href="../../css/styles/checklistManager.css">

    <title>Checklist Manager</title>
  </head>

  <body>
    <?php
      require_once("../templates/modals/checklistPublication.php");
      require_once("../templates/modals/shareChecklist.php");
    ?>

    <?php require_once('../templates/navbar.php'); ?>

    <div class="checklistHasBeenPublishedMessage"></div>

    <div class="container mt-5 mb-5">
      
      <div class="checklist-infos">
        <a href="../../index.php">
          <i class="fas fa-chevron-left"></i>
        </a>
        <h1 class="text-justify"><?= $checklist->getTitle() ?></h1>
      </div>
      <p class="lead text-justify"><?= $checklist->getDescription() ?></p>
      <p class="text-muted">Created by <?= $checklist->getAuthorName($conn) ?>.</p>

      <?php if ($checklist->getAuthorId() == $_SESSION["USER_ID"]) {?>
        <button class="btn btn-primary" data-toggle="modal" data-target="#shareChecklist">
          <span class="mr-2">
            <i class="fas fa-share"></i>
          </span>
          Share Checklist
      </button>
      <?php } ?>
        
      <hr>

      <?php if(!$checklist->getIsPublished()) { ?>
        <div class="alert alert-info">
          Edit your checklist sections to add some items to evaluate before publishing.
        </div>
      <?php } ?>

      <?php $sectionsResult = $checklist->getChecklistSections($conn, $checklist->getId()); ?>

      <?php
        while ($sectionRow = $sectionsResult->fetch_assoc()) {
          require("../templates/checklistManager/sectionCard.php");
        }
      ?>

      <?php if(!$checklist->getIsPublished()) { ?>
        <div class="d-flex justify-content-center">
          <button id="publishChecklistBtn" class="btn btn-primary" data-toggle="modal" data-target="#checklistPublication">
              <span class="mr-1">
                  <i class="fas fa-check"></i>
              </span>
              Publish Checklist
          </button>
        </div>
      <?php } ?>

    </div>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../../js/jquery-3.5.1.js"></script>
    <script src="../../js/popper-base.js"></script>
    <script src="../../js/bootstrap/bootstrap.js"></script>

    <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>

    <script src="../../js/pages/checklistManager/publishChecklist.js"></script>
  </body>
</html>
