let lastNItemInputGroups = 1;

function itemsManager(currentNItemInputGroups) {
  if (currentNItemInputGroups > lastNItemInputGroups) {
    addItem(currentNItemInputGroups);
  } else {
    if (currentNItemInputGroups > 0) {
      delItem(currentNItemInputGroups);
    }
  }
}

function addItem(currentNItemInputGroups) {
  const itemInputs = document.querySelector("#itemInputs");

  const newInput = document.createElement("div");
  newInput.innerHTML = `
    <div class="row" id="itemInputGroup-${currentNItemInputGroups}">
      <div class="form-group col-md-8">
        <label>Item ${currentNItemInputGroups} - Title</label>
        <input type="text" class="form-control" name="itemTitles[]">
      </div>
      <div class="form-group col-md-4">
        <label>Reference Link</label>
        <input type="url" class="form-control" name="referenceLinks[]">
        <small class="text-muted">Optional Field</small>
      </div>
    </div>
  `;

  itemInputs.append(newInput);

  lastNItemInputGroups = currentNItemInputGroups;
}

function delItem(currentNItemInputGroups) {
  const inputToDelete = document.querySelector(
    `#itemInputGroup-${currentNItemInputGroups + 1}`
  );
  inputToDelete.remove();

  lastNItemInputGroups = currentNItemInputGroups;
}

function submitForm() {
  const form = document.querySelector("form");
  form.submit();
}
