function loadItemId(itemId) {
  const editBtn = document.querySelector(
    "#deleteButtonGroup button[type='submit']"
  );
  editBtn.id = `editBtn-${itemId}`;
}

function deleteItem(itemId) {
  if (confirm) {
    $.ajax({
      method: "POST",
      url: "../../php/controllers/sectionEditor/deleteItem.php",
      data: {
        itemId,
      },
    }).done((data) => {
      const response = JSON.parse(data);

      const wasSuccess = response.status === "success";
      const message = response.message;

      const alertClass = wasSuccess ? "success" : "warning";
      const messagePrefix = wasSuccess ? "Success!" : "Warning!";

      const messageContainer = document.querySelector(
        ".itemHasBeenUpdatedMessage"
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
        const deletedItem = document.querySelector(`#item-${itemId}`);
        deletedItem.parentNode.remove();
      }

      $("#deleteItem").modal("hide");
    });
  }
}
