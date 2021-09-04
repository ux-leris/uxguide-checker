const nSectionsDisplay = document.querySelector("#nSections");

let lastNSectionInputs = 1;

function sectionManager(currentNSectionInputs) {
  if (currentNSectionInputs > lastNSectionInputs) {
    addSection(currentNSectionInputs);
  } else {
    if (currentNSectionInputs > 0) {
      delSection(currentNSectionInputs);
    }
  }
}

function addSection(currentNSectionInputs) {
  const sectionInputs = document.querySelector("#sectionInputs");

  const newInput = document.createElement("div");
  newInput.innerHTML = `
    <div id="sectionTitleInput-${currentNSectionInputs}" class="form-group">
      <label>Section ${currentNSectionInputs} - Title</label>
      <input type="text" class="form-control" name="sectionTitles[]"></input>
    </div>
  `;

  sectionInputs.append(newInput);

  lastNSectionInputs = currentNSectionInputs;
  nSectionsDisplay.innerHTML = currentNSectionInputs;
}

function delSection(currentNSectionInputs) {
  const inputToDelete = document.querySelector(
    `#sectionTitleInput-${currentNSectionInputs + 1}`
  );
  inputToDelete.remove();

  lastNSectionInputs = currentNSectionInputs;
  nSectionsDisplay.innerHTML = currentNSectionInputs;
}
