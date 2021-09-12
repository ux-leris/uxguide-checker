<?php
  require_once("php/classes/database.class.php");
  require_once("php/dataAccess/checklistDAO.class.php");

  session_start();

  if(!isset($_SESSION["USER_ID"])) {
    header("location: ./php/pages/signIn.php");
  } else {
    $conn = Database::connect();

    $checklistDAO = new ChecklistDAO;
  }
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./css/bootstrap/bootstrap.css">

    <!-- CSS Local -->
    <link rel="stylesheet" href="./css/styles/global.css">
    <link rel="stylesheet" href="./css/styles/index.css">

    <title>Homepage</title>
  </head>

  <body>
    <?php
      require_once("./php/templates/modals/createChecklistForm.php");
      require_once("./php/templates/modals/evaluationWarning.php");
    ?>

    <nav class="navbar navbar-expand-lg navbar-light">
      <span class="navbar-brand text-light">
        UX Guide Checker
      </span>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <button class="btn btn-primary dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown">
              Create
            </button>
            <div class="dropdown-menu">
              <a href="#" class="dropdown-item" data-toggle="modal" data-target="#createChecklistForm">
                Checklist
              </a>
            </div>
          </li>
        </ul>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link text-light" href="./php/controllers/index/logOut.php">
              Log out
            </a>						
          </li>
        </ul>
      </div>
    </nav>

    <div class="container mt-5 mb-5">
      <h1>My checklists</h1>
            
      <?php $result = checklistDAO::getUserChecklists($conn, $_SESSION["USER_ID"]); ?>

      <?php if($result->num_rows > 0) { ?>
        <div class="row mt-3">
          <?php
            while($row = $result->fetch_assoc()) {
              require("./php/templates/index/checklistCard.php");
            }
          ?>
        </div>
      <?php } ?>

      <?php $result = checklistDAO::getSharedChecklists($conn, $_SESSION["USER_ID"]); ?>

      <?php if($result->num_rows > 0) { ?>
        <h1 class="mt-5">Shared With You</h1>
        <div class="row mt-3">
          <?php
            while($row = $result->fetch_assoc()) {
              require("./php/templates/index/checklistCard.php");
            }
          ?>
        </div>
      <?php } ?>

    </div>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="./js/jquery-3.5.1.js"></script>
    <script src="./js/popper-base.js"></script>
    <script src="./js/bootstrap/bootstrap.js"></script>

    <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>

    <script src="./js/pages/index/createChecklistForm.js"></script>
    <script src="./js/pages/index/sectionsManager.js"></script>
    <script src="./js/pages/index/optionsManager.js"></script>
    <script src="./js/pages/index/setEvaluationLink.js"></script>
  </body>
</html>