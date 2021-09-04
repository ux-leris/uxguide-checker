const nOptionsDisplay = document.querySelector("#nOptions");

let lastNOptionInputs = 2;

function optionsManager(currentNOptionInputs) {
  if (currentNOptionInputs > lastNOptionInputs) {
    addOption(currentNOptionInputs);
  } else {
    if (currentNOptionInputs > 1) {
      delOption(currentNOptionInputs);
    }
  }
}

function addOption(currentNOptionInputs) {
  const optionInputs = document.querySelector("#optionInputs");

  const newInput = document.createElement("div");
  newInput.innerHTML = `
    <div id="optionTitleInput-${currentNOptionInputs}" class="form-group">
      <label>Option ${currentNOptionInputs} - Title</label>
      <input type="text" class="form-control" name="itemOptions[]"></input>
    </div>
    <div id="justificationCheck-${currentNOptionInputs}" class="form-group form-check ml-1">
      <input type="checkbox" class="form-check-input" name="needJustification[]" value="${currentNOptionInputs - 1}"></input>
      <label>This option require a justification</label>
    </div>
  `;

  optionInputs.append(newInput);

  lastNOptionInputs = currentNOptionInputs;
  nOptionsDisplay.innerHTML = currentNOptionInputs;
}

function delOption(currentNOptionInputs) {
  const textInputToDelete = document.querySelector(
    `#optionTitleInput-${currentNOptionInputs + 1}`
  );
  const checkboxInputToDelete = document.querySelector(
    `#justificationCheck-${currentNOptionInputs + 1}`
  );

  textInputToDelete.remove();
  checkboxInputToDelete.remove();

  lastNOptionInputs = currentNOptionInputs;
  nOptionsDisplay.innerHTML = currentNOptionInputs;
}
