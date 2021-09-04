function setEvaluationLink(checklistId) {
  const continueLink = document.querySelector("#linktToEvaluation");

  continueLink.href = `./php/pages/checklist.php?id=${checklistId}`;

  $("#evaluationWarning").modal("show");
}
