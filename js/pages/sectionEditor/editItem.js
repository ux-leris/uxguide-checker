const itemTitleInput = document.querySelector("#editItemTitleInput");
const referenceLinkInput = document.querySelector("#editReferenceLinkInput");

function loadItemInfos(itemId) {
  itemTitleInput.value = "";
  referenceLinkInput.value = "";

  const editBtn = document.querySelector(
    "#editButtonGroup button[type='submit']"
  );
  editBtn.id = `editBtn-${itemId}`;

  const itemToEdit = document.querySelector(`#item-${itemId} .card-body`);

  itemTitleInput.value = itemToEdit.innerText;

  if (!itemToEdit.querySelector("a")) {
    referenceLinkInput.placeholder = "This item doesn't have a reference link.";
  } else referenceLinkInput.value = itemToEdit.querySelector("a").href;
}

function filterId(rawItemId) {
  return rawItemId.split("-")[1];
}

function editItem(itemId) {
  const itemTitle = itemTitleInput.value;
  const referenceLink = referenceLinkInput.value || null;

  $.ajax({
    method: "POST",
    url: "../../php/controllers/sectionEditor/updateItem.php",
    data: {
      itemId,
      itemTitle,
      referenceLink,
    },
  }).done((data) => {
    const response = JSON.parse(data);

    const wasSuccess = response.status === "success";
    const message = response.message;

    const alertClass = wasSuccess ? "success" : "warning";
    const messagePrefix = wasSuccess ? "Success!" : "Warning!";

    const messageContainer = document.querySelector(
      ".responseMessage"
    );

    messageContainer.innerHTML = `
      <div class="alert alert-${alertClass} alert-dismissible rounded-0 fade show" role="alert">
        <strong>${messagePrefix}</strong> ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    `;

    if (wasSuccess) {
      const editedItem = document.querySelector(`#item-${itemId} .card-body`);
      editedItem.innerHTML = `
        <span>
          <i class="fas fa-grip-lines mr-3"></i>
        </span>
        ${
          !referenceLink
            ? itemTitle
            : `<a href="${referenceLink}" class="link" target="_blank">${itemTitle}</a>`
        }
      `;
    }

    $("#editItem").modal("hide");
  });
}
