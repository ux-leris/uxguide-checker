<?php
  require_once("../classes/database.class.php");
  require_once("../classes/checklist.class.php");
  require_once("../classes/section.class.php");
  require_once("../classes/evaluation.class.php");

  require_once("../dataAccess/itemDAO.class.php");
  require_once("../dataAccess/labelDAO.class.php");

  session_start();

  if (!isset($_SESSION["USER_ID"])) {
    header("location: ./signIn.php");
  }

  $conn = Database::connect();

  $evaluationId = $_GET["e_id"];

  $evaluation = new Evaluation($conn, $evaluationId);
  $checklist = new Checklist($conn, $evaluation->getChecklistId());

  if(!$evaluation->getId() || !$checklist->getId() || ($evaluation->getAuthorId() != $_SESSION["USER_ID"] && $checklist->getAuthorId() != $_SESSION["USER_ID"])) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
  }

  $date = date("d/m/Y", strtotime($evaluation->getDate()));
  $time = date("H:i", strtotime($evaluation->getDate()));

  $timeElapsed = $evaluation->getTimeElapsedInSeconds();
  $timeElapsedInHours = sprintf("%02d:%02d:%02d", floor($timeElapsed / 3600), ($timeElapsed / 60) % 60, $timeElapsed % 60);

  $evaluationAuthor = evaluationDAO::getAuthorName($conn, $evaluation->getId());
  $author = $evaluationAuthor->fetch_row();
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
    <link rel="stylesheet" href="../../css/styles/checklistResult.css">

    <title>Checklist Result</title>
  </head>

  <body>
    <?php include("../templates/navbar.php"); ?>

    <div class="container mt-5 mb-5">
      <div class="checklist-infos">
        <a href="../../index.php">
          <i class="fas fa-chevron-left"></i>
        </a>
        <h1 class="text-justify"><?= $checklist->getTitle() ?></h1>
      </div>
      <p class="lead text-justify"><?= $checklist->getDescription() ?></p>
      <p class="text-muted">Evaluated by <?= $author[0] ?> in <?= $date ?> at <?= $time ?> with duration of <?= $timeElapsedInHours ?></p>

      <?php if ($evaluation->getAuthorId() == $_SESSION['USER_ID']) { ?>
        <a href="./checklist.php?id=<?= $checklist->getId() ?>&e_id=<?= $evaluation->getId() ?>&edit" class="btn btn-primary">
          <span class="mr-1">
            <i class="fas fa-edit"></i>
          </span>
          Edit answers
        </a>
      <?php } ?>

      <hr>

      <?php $checklistResult = $checklist->getChecklistSections($conn, $checklist->getId()); ?>

      <?php while ($checklistRow = $checklistResult->fetch_assoc()) { ?>
        <div class="card mt-5 mb-5">
          <div class="card-header text-justify">
            <h3 class="text-justify">Section <?= $checklistRow["position"] + 1 ?></h3>
            <p class="lead text-muted text-justify"><?= $checklistRow["title"] ?></p>
          </div>

          <div class="card-body">
            <?php
              $section = new Section($conn, $checklistRow["id"]);
              $sectionResult = $section->getSectionItems($conn, $section->getId());

              while ($sectionRow = $sectionResult->fetch_assoc()) {
                $itemAnswerResult = ItemDAO::getItemAnswer($conn, $evaluation->getId(), $sectionRow["id"]);
                $itemAnswerRow = $itemAnswerResult->fetch_assoc();

                $labelTitle = (
                  $itemAnswerRow ? 
                    LabelDAO::getOptionTitle($conn, $itemAnswerRow["label"])
                  :
                    "This question didn't belong to the checklist when this evaluation was made"
                );
              ?>

              <div class="card mt-2 mb-2">
                <div class="card-body text-justify">
                  <h5 class="text-muted"><?= $labelTitle ?></h5>
                  <?= $sectionRow["text"] ?>
                </div>

                <?php if (isset($itemAnswerRow["justification"])) { ?>
                  <div class="card-footer text-justify">
                    <h5>Justification:</h5>
                    <?= $itemAnswerRow["justification"] ?>
                  </div>
                <?php } ?>
              </div>
            <?php } ?>

          </div>
        </div>
      <?php } ?>
    </div>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../../js/jquery-3.5.1.js"></script>
    <script src="../../js/popper-base.js"></script>
    <script src="../../js/bootstrap/bootstrap.js"></script>

    <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>
  </body>
</html>