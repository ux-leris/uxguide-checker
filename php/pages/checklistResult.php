<?php
require_once("../classes/database.class.php");
require_once("../classes/checklist.class.php");
require_once("../classes/section.class.php");
require_once("../classes/evaluation.class.php");
require_once("../dataAccess/itemDAO.class.php");
require_once("../dataAccess/labelDAO.class.php");

session_start();

if (!isset($_SESSION["USER_ID"])) {
    header("location: ./login.php");
}

$db = new Database;
$conn = $db->connect();

$evaluation_id = $_GET["e_id"];

$evaluation = new Evaluation;
$evaluation->loadEvaluation($conn, $evaluation_id);

$checklist = new Checklist;
$checklist->loadChecklist($conn, $evaluation->get_checklist_id());

if(!$evaluation->get_id() || !$checklist->get_id() || ($evaluation->get_author() != $_SESSION["USER_ID"] && $checklist->get_author() != $_SESSION["USER_ID"])) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
}

$section = new Section;

$itemDAO = new ItemDAO;

$labelDAO = new LabelDAO;
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
    <?php include('../templates/navbar.php'); ?>

    <div id="modal-share" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Share Checklist Result</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" id="emailAddress" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onClick="sendChecklistResult()">
                        <span class="ml-1 mr-2">
                            <i class="fas fa-paper-plane"></i>
                        </span>
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <div style="display: flex; align-items: center;">
            <a href="./checklistEvaluations.php?c_id=<?= $checklist->get_id() ?>" style="color:#8FAD88;"><i class="fas fa-chevron-left fa-lg mr-3"></i></a>
            <h1><?= $checklist->get_title() ?></h1>
        </div>
        <p class="lead text-muted text-justify"><?= $checklist->get_description() ?></p>
        <p>

            <?php
            $date = date("d/m/Y", strtotime($evaluation->get_date()));
            $time = date("H:i:s", strtotime($evaluation->get_date()));
            ?>

            Evaluated by <?= $evaluation->get_authorName($conn) ?> in <?= $date ?> at <?= $time ?>.
        </p>
        <button type="button " class="btn btn-primary" data-toggle="modal" data-target="#modal-share">
            <span class="ml-1 mr-2">
                <i class="fas fa-share-alt"></i>
            </span>
            Share Result
        </button>

        <?php
        if ($evaluation->get_author() == $_SESSION['USER_ID']) {
        ?>
            <a href="./checklist.php?id=<?= $checklist->get_id() ?>&e_id=<?= $evaluation->get_id() ?>" class="btn btn-primary">
                <span class="ml-1 mr-2">
                    <i class="fas fa-edit"></i>
                </span>
                Editar respostas
            </a>
        <?php } ?>

        <?php
        $sectionResult = $checklist->loadSectionsOfChecklist($conn, $checklist->get_id());

        while ($sectionRow = $sectionResult->fetch_assoc()) {
        ?>

            <div class="card mt-5 mb-5">
                <div class="card-header text-justify">
                    <h3>Section <?= $sectionRow["position"] + 1 ?></h3>
                    <p class="lead text-muted"><?= $sectionRow["title"] ?></p>
                </div>
                <div class="card-body">

                    <?php
                    $section->loadSection($conn, $sectionRow["id"]);

                    $itemResult = $section->loadSectionItems($conn, $section->get_id());

                    while ($itemRow = $itemResult->fetch_assoc()) {

                        $itemAnswerResult = $itemDAO->select_itemAnswer($conn, $evaluation->get_id(), $itemRow["id"]);

                        $itemAnswerRow = $itemAnswerResult->fetch_assoc();

                        if($itemAnswerRow) {
                            $labelTitle = $labelDAO->select_labelTitle($conn, $itemAnswerRow["label"]);
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

    <script type="text/javascript">
        function sendChecklistResult() {
            var address = $("#emailAddress").val();

            $.ajax({
                type: "POST",
                url: "../controllers/send_checklistResult.php",
                data: {
                    emailAddress: address
                },
                success: function(response) {
                    alert("E-mail enviado com sucesso!");

                    $("#modal-share").modal("hide");
                }
            })
        }
    </script>

</body>

</html>