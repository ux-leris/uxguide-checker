<?php
  $date = date("d/m/Y", strtotime($evaluationRow["date"]));
  $time = date("H:i", strtotime($evaluationRow["date"]));

  $timeElapsed = $evaluationRow["time_elapsed"];
  $timeElapsedInHours = sprintf("%02dh:%02dm:%02ds", floor($timeElapsed / 3600), ($timeElapsed / 60) % 60, $timeElapsed % 60);

  $evaluationAuthor = evaluationDAO::getAuthorName($conn, $evaluationRow["id"]);
  $author = $evaluationAuthor->fetch_row();
?>

<div class="col-md-6 mt-2 mb-2">
  <div class="card shadow">
    <div class="card-body">
      <?php if(!$evaluationRow["status"]) { ?>
        <span class="badge badge-pill badge-warning mt-2 mb-2">Pending</span>
      <?php } else { ?>
        <span class="badge badge-pill badge-success mt-2 mb-2">Done</span>
      <?php } ?>

      <h5>Evaluation <?= $evaluationRow["id"] ?></h5>
      <p>Evaluated by <?= $author[0] ?> in <?= $date ?> at <?= $time ?></p>
      <p class="text-muted">Time to evaluate: <?= $timeElapsedInHours ?></p>

      <div class="d-flex justify-content-start">
        <?php if(!$evaluationRow["status"]) { ?>
          <a href="./checklist.php?id=<?= $checklistId ?>&e_id=<?= $evaluationRow["id"] ?>" class="btn btn-primary">
            <span class="mr-2">
              <i class="fas fa-play"></i>
            </span>
            Resume Evaluation
          </a>
        <?php } else { ?>
          <a href="./checklistResult.php?e_id=<?= $evaluationRow["id"] ?>" class="btn btn-primary">
            <span class="mr-2">
              <i class="fas fa-poll-h"></i>
            </span>
            View Results
          </a>
        <?php } ?>
      </div>
    </div>
  </div>
</div>