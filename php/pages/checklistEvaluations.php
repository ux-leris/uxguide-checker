<?php
  require_once("../classes/database.class.php");
  require_once("../classes/checklist.class.php");
  require_once("../dataAccess/evaluationDAO.class.php");

  session_start();

  if(!isset($_SESSION["USER_ID"])) {
    header("location: ./signIn.php");
  }

  $conn = Database::connect();

  $checklistId = $_GET["c_id"];

  $checklist = new Checklist($conn, $checklistId);

  if(!$checklist->getId() || !$checklist->userHasAccess($conn, $_SESSION["USER_ID"])) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
  }

  $hasFinishedEval = false;
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
    <link rel="stylesheet" href="../../css/styles/checklistEvaluations.css">

    <title>Checklist Evaluations</title>
  </head>

  <body>
    <?php include("../templates/navbar.php"); ?>

    <div class="container mt-5 pb-5">
      <div class="checklist-infos">
        <a href="../../index.php">
          <i class="fas fa-chevron-left"></i>
        </a>
        <h1 class="text-justify"><?= $checklist->getTitle() ?></h1>
      </div>
      <p class="lead text-justify"><?= $checklist->getDescription() ?></p>
      <p class="text-muted">Created by <?= $checklist->getAuthorName($conn) ?>.</p>
      
      <?php if ($checklist->getAuthorId() == $_SESSION["USER_ID"]) {?>
        <a href="./checklistAnalytics.php?c_id=<?= $checklistId ?>" class="btn btn-primary">
          <span class="mr-2">
            <i class="far fa-chart-bar"></i>
          </span>
          View Analytics
        </a>
      <?php } ?>

      <hr>

      <h3 class="text-muted mb-3"><?= $checklist->getAuthorId() == $_SESSION["USER_ID"] ? "All Evaluations" : "My Evaluations" ?></h3>

      <?php
        if ($checklist->getAuthorId() == $_SESSION["USER_ID"]) {
          $evaluationResult = evaluationDAO::getAllFinishedEvaluations($conn, $checklistId, $_SESSION["USER_ID"]);
        } else {
          $evaluationResult = evaluationDAO::getEvaluationsOfChecklistByUser($conn, $checklistId, $_SESSION["USER_ID"]);
        }
      ?>

      <div class="row">
        <?php
          while($evaluationRow = $evaluationResult->fetch_assoc()) {
            require("../templates/checklistEvaluations/evaluationCard.php");
          }
        ?>
      </div>
    </div>
            
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../../js/jquery-3.5.1.js"></script>
    <script src="../../js/popper-base.js"></script>
    <script src="../../js/bootstrap/bootstrap.js"></script>

    <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>
  </body>
</html>