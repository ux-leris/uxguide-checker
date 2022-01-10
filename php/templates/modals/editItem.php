<div id="editItem" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Item</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Item Title</label>
          <input type="text" id="editItemTitleInput" class="form-control">
        </div>
        <div class="form-group">
          <label>Reference Link</label>
          <input type="text" id="editReferenceLinkInput" class="form-control">
          <small class="text-muted">Optional field</small>
        </div>
      </div>
      <div id="editButtonGroup" class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button id="editBtn-null" type="submit" class="btn btn-primary" onClick="editItem(filterId(this.id))">Save changes</button>
      </div>
    </div>
  </div>
</div>