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

  $evaluation_id = $_GET["e_id"];

  $evaluation = new Evaluation($conn, $evaluation_id);

  $checklist = new Checklist($conn, $evaluation->getChecklistId());

  if(!$evaluation->getId() || !$checklist->getId() || ($evaluation->getAuthorId() != $_SESSION["USER_ID"] && $checklist->getAuthorId() != $_SESSION["USER_ID"])) {
      header("HTTP/1.0 404 Not Found");
      echo "<h1>404 Not Found</h1>";
      echo "The page that you have requested could not be found.";
      exit();
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

    <title><?= $checklist->getTitle() ?> checklist</title>
</head>

<body>
    <!-- Navbar -->
    <?php include('../templates/navbar.php'); ?>

    <div class="container mt-5 mb-5">
        <div style="display: flex; align-items: center;">
            <a href="./checklistEvaluations.php?c_id=<?= $checklist->getId() ?>" style="color:#8FAD88;"><i class="fas fa-chevron-left fa-lg mr-3"></i></a>
            <h1><?= $checklist->getTitle() ?></h1>
        </div>
        <p class="lead text-muted text-justify"><?= $checklist->getDescription() ?></p>
        <?php
  $date = date("d/m/Y", strtotime($evaluation->getDate()));
  $time = date("H:i", strtotime($evaluation->getDate()));

  $timeElapsed = $evaluation->getTimeElapsedInSeconds();
  $timeElapsedInHours = sprintf("%02dh:%02dm:%02ds", floor($timeElapsed / 3600), ($timeElapsed / 60) % 60, $timeElapsed % 60);

  $evaluationAuthor = evaluationDAO::getAuthorName($conn, $evaluation->getId());
  $author = $evaluationAuthor->fetch_row();
?>



<p>Evaluated by <?= $author[0] ?> in <?= $date ?> at <?= $time ?></p>
      <p class="text-muted">Time to evaluate: <?= $timeElapsedInHours ?></p>


        <?php
        if ($evaluation->getAuthorId() == $_SESSION['USER_ID']) {
        ?>
            <a href="./checklist.php?id=<?= $checklist->getId() ?>&e_id=<?= $evaluation->getId() ?>&edit" class="btn btn-primary">
                <span class="ml-1 mr-2">
                    <i class="fas fa-edit"></i>
                </span>
                Edit answers
            </a>
        <?php } ?>

        <?php
        $sectionResult = $checklist->getChecklistSections($conn, $checklist->getId());

        while ($sectionRow = $sectionResult->fetch_assoc()) {
        ?>

            <div class="card mt-5 mb-5">
                <div class="card-header text-justify">
                    <h3>Section <?= $sectionRow["position"] + 1 ?></h3>
                    <p class="lead text-muted"><?= $sectionRow["title"] ?></p>
                </div>
                <div class="card-body">

                    <?php
                    $section = new Section($conn, $sectionRow["id"]);
                    $itemResult = $section->getSectionItems($conn, $section->getId());

                    while ($itemRow = $itemResult->fetch_assoc()) {

                        $itemAnswerResult = ItemDAO::getItemAnswer($conn, $evaluation->getId(), $itemRow["id"]);

                        $itemAnswerRow = $itemAnswerResult->fetch_assoc();

                        if($itemAnswerRow) {
                            $labelTitle = LabelDAO::getOptionTitle($conn, $itemAnswerRow["label"]);
                        } else {
                            $labelTitle = "This question did not belong to the checklist when this evaluation was made.";
                        }

                        
                    ?>

                        <div class="card mt-3 mb-3">
                            <div class="card-body text-justify">
                                <h5 class="text-muted">
                                    <?= $labelTitle ?>
                                </h5>
                                <?= $itemRow["text"] ?>
                            </div>

                            <?php
                            if (isset($itemAnswerRow["justification"])) {
                            ?>

                                <div class="card-footer text-justify">
                                    <h5>Justification:</h5>
                                    <?= $itemAnswerRow["justification"] ?>
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