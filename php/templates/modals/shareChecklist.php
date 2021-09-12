<div id="shareChecklist" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="../controllers/checklistManager/shareChecklist.php?c_id=<?= $checklistId ?>" class="col-md-12">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Share Checklist</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>User E-mail</label>
            <input type="email" class="form-control" name="email" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Share</button>
        </div>
      </div>
    </form>
  </div>
</div>