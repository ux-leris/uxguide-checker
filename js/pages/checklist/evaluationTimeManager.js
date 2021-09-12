let timeElapsedInSeconds = 0;

function startChronometer(evaluationId) {
  setInterval(() => {
    timeElapsedInSeconds++;
  }, 1000);

  timeManager(evaluationId);
}

function timeManager(evaluationId) {
  document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "hidden") {
      $.ajax({
        method: "POST",
        url: "../../php/controllers/checklist/updateTime.php",
        data: {
          e_id: evaluationId,
          timeElapsedInSeconds,
        },
      });
    } else {
      timeElapsedInSeconds = 0;
    }
  });
}
