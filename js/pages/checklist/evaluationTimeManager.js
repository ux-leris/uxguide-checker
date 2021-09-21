let timeElapsedInSeconds = 0;

function startChronometer(evaluationId) {
  console.log("iniciou")
  setInterval(() => {
    console.log(timeElapsedInSeconds);
    timeElapsedInSeconds++;
  }, 1000);

  timeManager(evaluationId);
}

// function timeManager(evaluationId) {
//   document.addEventListener("visibilitychange", () => {
//     if (document.visibilityState === "hidden") {
//       $.ajax({
//         method: "POST",
//         url: "../../php/controllers/checklist/updateTime.php",
//         data: {
//           e_id: evaluationId,
//           timeElapsedInSeconds,
//         },
//       });
//     } else {
//       timeElapsedInSeconds = 0;
//     }
//   });
// }

function timeManager(evaluationId) {
  document.addEventListener('visibilitychange', function saveTime() {
    if (document.visibilityState === 'hidden') {
      var data = new FormData();
      data.append('e_id', evaluationId);
      data.append('timeElapsedInSeconds', timeElapsedInSeconds);
      navigator.sendBeacon("../../php/controllers/checklist/updateTime.php", data); 
    }
  });
}
			
	