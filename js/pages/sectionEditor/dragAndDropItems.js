const itemsContainer = document.querySelector("#checklistItems");

Sortable.create(itemsContainer, {
  onEnd: () => {
    const items = itemsContainer.children;

    let i = 0;

    for (let item of items) {
      const itemId = filterId(item.firstElementChild.id);
      const currPosition = item.getAttribute("item-position");

      if (currPosition !== i + 1) {
        $.ajax({
          method: "POST",
          url: "../../php/controllers/sectionEditor/updateItemPosition.php",
          data: {
            itemId,
            newPosition: i + 1,
          },
        }).done((data) => {
          const response = JSON.parse(data);

          const wasSuccess = response.status === "success";
          const message = response.message;

          const alertClass = wasSuccess ? "success" : "warning";
          const messagePrefix = wasSuccess ? "Success!" : "Warning!";

          const messageContainer = document.querySelector(".responseMessage");

          messageContainer.innerHTML = `
            <div class="alert alert-${alertClass} alert-dismissible rounded-0 fade show" role="alert">
              <strong>${messagePrefix}</strong> ${message}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          `;

          if (wasSuccess) {
            item.setAttribute("item-position", i + 1);
          }
        });
      }

      i++;
    }
  },
});
