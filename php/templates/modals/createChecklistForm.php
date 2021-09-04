<div id="createChecklistForm" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="cadastrarChecklist" method="POST" action="php/controllers/insert_checklist.php">

        <div class="tab">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
          </div>
          <div class="modal-body">
            <h4 class="modal-title text-muted mb-3">Step 1</h4>
            <hr>
            <div class="form-group">
              <label>Checklist Title</label>
              <input type="text" name="title" class="form-control">
            </div>
            <div class="form-group">
              <label>Checklist Description</label>
              <input type="text" name="description" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="next()">Next</button>
          </div>
        </div>

        <div class="tab">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
          </div>
          <div class="modal-body">
            <h4 class="modal-title text-muted mb-2">Step 2</h4>
            <p class="mb-0">Set up your checklist sections</p>
            <hr>
            <div class="form-group">
              <div id="sectionTitleFields-area">
                <div id="sectionTitleField-1" class="form-group">
                  <label>Section 1 - Title</label>
                  <input type="text" name="sectionTitles[]" class="form-control">
                </div>
              </div>
              <div class="d-flex justify-content-center">
                <div class="d-flex">
                  <button type="button" class="btn btn-info mr-3" onclick="sectionTitlefieldsController(lastNSectionTitleFields - 1)">
                    <span class="mr-2">
                      <i class="fas fa-minus"></i>
                    </span>
                    Del
                  </button>
                </div>
                <div class="d-flex align-items-center justify-content-center" id="qtdSections">1</div>
                <div class="d-flex">
                  <button type="button" class="btn btn-info ml-3" onclick="sectionTitlefieldsController(lastNSectionTitleFields + 1)">
                    <span class="mr-2">
                      <i class="fas fa-plus"></i>
                    </span>
                    Add
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="previous()">Previous</button>
            <button type="button" class="btn btn-primary" onclick="next()">Next</button>
          </div>
        </div>

        <div class="tab">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              &times;
            </button>
          </div>
          <div class="modal-body">
            <h4 class="modal-title text-muted mb-2">Step 3</h4>
            <p class="mb-0">Set up your checklist answer option</p>
            <hr>
            <div class="form-group">
              <div id="itemLabelFields-area">
                <div class="form-group">
                  <label>Option 1 - Text</label>
                  <input type="text" name="itemLabels[]" class="form-control">											
                </div>
                <div class="form-group form-check ml-1">
                  <input type="checkbox" name="hasJustification[]" class="form-check-input" value="0">
                  <label>Need justification.</label>
                </div>
                <div class="form-group">
                  <label>Option 2 - Text</label>
                  <input type="text" name="itemLabels[]" class="form-control">											
                </div>
                <div class="form-group form-check ml-1">
                  <input type="checkbox" name="hasJustification[]" class="form-check-input" value="1">
                  <label>Need justification.</label>
                </div>
              </div>
              <div class="d-flex justify-content-center">
                <div class="d-flex">
                  <button type="button" class="btn btn-info mr-3" onclick="itemLabelFieldsController(lastNItemLabelFields - 1)">
                    <span class="mr-2">
                      <i class="fas fa-minus"></i>
                    </span>
                    Del
                  </button>
                </div>
                <div class="d-flex align-items-center justify-content-center" id="qtdItemLabels">1</div>
                <div class="d-flex">
                  <button type="button" class="btn btn-info ml-3" onclick="itemLabelFieldsController(lastNItemLabelFields + 1)">
                    <span class="mr-2">
                      <i class="fas fa-plus"></i>
                    </span>
                    Add
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="previous()">Previous</button>
            <button type="button" class="btn btn-primary" onclick="submitForm()">Create</button>
          </div>
        </div>

        <div class="col-md-12 text-center mt-3 mb-3">
          <span class="step"></span>
          <span class="step"></span>
          <span class="step"></span>
        </div>
        
      </form>
    </div>
  </div>
</div>