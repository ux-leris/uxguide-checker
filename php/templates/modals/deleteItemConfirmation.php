<div id="deleteItem" class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Warning</h5>
        <button type="button" class="close" data-dismiss="modal">
          &times;
        </button>
      </div>
      <div class="modal-body">
        <p>
          You are about to delete an item, are you sure you want to continue?
        </p>
      </div>
      <div id="deleteButtonGroup" class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Close
        </button>
        <button type="submit" class="btn btn-primary" onClick="deleteItem(filterId(this.id))">
          Delete Item
        </button>
      </div>
    </div>
  </div>
</div>