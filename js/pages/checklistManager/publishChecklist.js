function publishChecklist(checklistId) {
  $.ajax({
    method: "POST",
    url: "../controllers/checklistManager/publishChecklist.php",
    data: {
      c_id: checklistId,
    },
  }).done((data) => {
    const response = JSON.parse(data);
    console.log(response);

    const wasSuccess = response.status === "success";
    const message = response.message;

    const alertClass = wasSuccess ? "success" : "warning";
    const messagePrefix = wasSuccess ? "Success!" : "Warning!";

    const messageContainer = document.querySelector(
      ".checklistHasBeenPublishedMessage"
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
      const publishChecklistBtn = document.querySelector(
        "#publishChecklistBtn"
      );
      publishChecklistBtn.style.display = "none";
    }

    $("#checklistPublication").modal("hide");
  });
}
