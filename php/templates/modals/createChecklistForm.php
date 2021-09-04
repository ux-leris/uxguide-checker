<div id="createChecklistForm" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="createChecklist" method="POST" action="php/controllers/index/createChecklist.php">

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
              <div id="sectionInputs">
                <div id="sectionTitleInput-1" class="form-group">
                  <label>Section 1 - Title</label>
                  <input type="text" class="form-control" name="sectionTitles[]">
                </div>
              </div>
              <div class="d-flex justify-content-center">
                <div class="d-flex">
                  <button type="button" class="btn btn-info mr-3" onclick="sectionManager(lastNSectionInputs - 1)">
                    <span class="mr-2">
                      <i class="fas fa-minus"></i>
                    </span>
                    Del
                  </button>
                </div>
                <div id="nSections" class="d-flex align-items-center justify-content-center">1</div>
                <div class="d-flex">
                  <button type="button" class="btn btn-info ml-3" onclick="sectionManager(lastNSectionInputs + 1)">
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
              <div id="optionInputs">
                <div class="form-group">
                  <label>Option 1 - Text</label>
                  <input type="text" id="optionTitleInput-1" class="form-control" name="itemOptions[]">											
                </div>
                <div class="form-group form-check ml-1">
                  <input type="checkbox" id="justificationCheck-1" class="form-check-input" name="needJustification[]" value="0">
                  <label>This option require a justification.</label>
                </div>
                <div class="form-group">
                  <label>Option 2 - Text</label>
                  <input type="text" id="optionTitleInput-2" name="itemOptions[]" class="form-control">											
                </div>
                <div class="form-group form-check ml-1">
                  <input type="checkbox" id="justificationCheck-2" class="form-check-input" name="needJustification[]" value="1">
                  <label>This option require a justification.</label>
                </div>
              </div>
              <div class="d-flex justify-content-center">
                <div class="d-flex">
                  <button type="button" class="btn btn-info mr-3" onclick="optionsManager(lastNOptionInputs - 1)">
                    <span class="mr-2">
                      <i class="fas fa-minus"></i>
                    </span>
                    Del
                  </button>
                </div>
                <div class="d-flex align-items-center justify-content-center" id="nOptions">2</div>
                <div class="d-flex">
                  <button type="button" class="btn btn-info ml-3" onclick="optionsManager(lastNOptionInputs + 1)">
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