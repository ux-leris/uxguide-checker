<div class="card mt-4 mb-4">
  <div class="card-header d-flex">
    <div class="mr-auto">
      <h3>Section <?= $sectionRow["position"] + 1 ?></h3>
    </div>
    <div class="ml-auto">
      <a class="btn btn-secondary" href="./sectionEditor.php?s_id=<?= $sectionRow["id"] ?>">
        <span class="mr-1">
          <i class="fas fa-cog"></i>
        </span>
        Edit Section
      </a>
    </div>
  </div>
  <div class="card-body text-justify">
    <?= $sectionRow["title"] ?>
  </div>
</div>