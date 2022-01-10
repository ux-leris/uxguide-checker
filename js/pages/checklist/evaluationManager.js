function startEvaluation(isEdition, evaluationId, checklistId) {
  if (!isEdition) startChronometer(evaluationId);

  $.ajax({
    method: "GET",
    url: "../../php/controllers/checklist/getJustifiableOptions.php",
    data: {
      c_id: checklistId,
    },
  }).done((data) => {
    renderCollapsedAreas(eval(data));
  });

  const options = document.querySelectorAll(".selectOption");
  options.forEach((element) => {
    element.addEventListener("change", (event) => {
      $.ajax({
        method: "POST",
        url: "../../php/controllers/checklist/updateAnswers.php",
        data: {
          e_id: evaluationId,
          i_id: filterId(event.target.id),
          o_id: event.target.value,
        },
      });
    });
  });

  const justifications = document.querySelectorAll(".justificationInput");
  justifications.forEach((element) => {
    element.addEventListener("change", (event) => {
      $.ajax({
        method: "POST",
        url: "../../php/controllers/checklist/updateAnswers.php",
        data: {
          e_id: evaluationId,
          i_id: filterId(event.target.id),
          justification: event.target.value,
        },
      });
    });
  });
}

function filterId(rawItemId) {
  return rawItemId.split("-")[1];
}
