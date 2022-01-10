<div id="checklistPublication" class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Publish Checklist</h5>
        <button type="button" class="close" data-dismiss="modal">
          &times;
        </button>
      </div>
      <div class="modal-body">
        <p>
          You will not be able to remove your checklist items after publishing, but you can add more items if you need.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Close
        </button>
        <button type="button" class="btn btn-primary" onClick="publishChecklist(<?= $checklist->getId() ?>)">
          Continue
        </button>
      </div>
    </div>
  </div>
</div>