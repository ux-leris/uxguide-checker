<div class="col-md-4">
  <div class="card mt-3 mb-3 shadow">

    <div class="card-header">
      <h5><?= $row["title"] ?></h5>
    </div>

    <div class="card-body">
      <p class="text-justify">
        <?= $row["description"] ?>
      </p>
      <div class="d-flex">
        <div class="mr-auto">
          <button
            class="btn btn-primary mr-1"
            onClick="setEvaluationLink(<?= $row["id"] ?>)"
            <?= !$row["published"] ? "disabled" : null ?>
          >
            Evaluate Checklist
          </button>

          <a 
            href="./php/pages/checklistEvaluations.php?c_id=<?= $row["id"] ?>"
            class="btn btn-secondary ml-1 <?= !$row["published"] ? "disabled" : null ?>"
          >
            <span class="mr-1">
              <i class="fas fa-check-circle"></i>
            </span>
            Evaluations
          </a>
        </div>
      </div>
    </div>

    <?php if($row["author_id"] == $_SESSION["USER_ID"]) { ?>
      <div class="card-footer d-flex justify-content-center">
        <a href="./php/pages/checklistManager.php?c_id=<?= $row["id"] ?>" class="manage-link">
          <span class="mr-1">
            <i class="fas fa-edit"></i>
          </span>
          Manage
        </a>
      </div>
    <?php } ?>

  </div>
</div>