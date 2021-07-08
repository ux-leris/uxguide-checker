<?php
    require_once("../classes/database.class.php");
    require_once("../classes/checklist.class.php");
    require_once("../dataAccess/evaluationDAO.class.php");

    session_start();

    if(!isset($_SESSION["USER_ID"]))
    {
      header("location: ./login.php");
    }

    $db = new Database;
    $conn = $db->connect();

    $checklist_id = $_GET["c_id"];

    $checklist = new Checklist;
    $checklist->loadChecklist($conn, $checklist_id);

    if(!$checklist->get_id() || $checklist->get_author() != $_SESSION["USER_ID"]) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Accept: application/json'
    ));

    // Set the url
    $url = "http://localhost/applications/uxguide-checker/php/api/answersBySections.php?c_id=$checklist_id";
    curl_setopt($ch, CURLOPT_URL, $url);

    // Get answers by sections
    $result_answers = curl_exec($ch);

    $url = "http://localhost/applications/uxguide-checker/php/api/infoNumbers.php?c_id=$checklist_id";
    curl_setopt($ch, CURLOPT_URL, $url);
  
    // Get info numbers
    $result_infoNumbers = curl_exec($ch);

    $url = "http://localhost/applications/uxguide-checker/php/api/overview.php?c_id=$checklist_id";
    curl_setopt($ch, CURLOPT_URL, $url);

    $resultOverview = curl_exec($ch);

    // Closing
    curl_close($ch);
    
    // Removing UTF-8 Bom 
    $result_answers = str_replace("\xEF\xBB\xBF",'',$result_answers); 
    $result_infoNumbers = str_replace("\xEF\xBB\xBF",'',$result_infoNumbers); 

    $resultOverview = str_replace("\xEF\xBB\xBF", "", $resultOverview); 

    // Decoding
    $answersBySections = json_decode($result_answers, true);
    $infoNumbers = json_decode($result_infoNumbers, true);

    $overview = json_decode($resultOverview, true);

    $avg = $infoNumbers['average_time'];
    $minutes = floor($avg / 60);
    $avg -= $minutes*60;
    $seconds = $avg;
    $average_time = sprintf("%02d:%02d", $minutes, $seconds);

    if($infoNumbers['total_finished_evaluations'] > 0) {
      $last_evaluation_time = $infoNumbers['finished_evaluations'][$infoNumbers['total_finished_evaluations']-1];
    } else {
      $last_evaluation_time = 0;
    }
    
    $minutes = floor($last_evaluation_time / 60);
    $last_evaluation_time -= $minutes*60;
    $seconds = $last_evaluation_time;
    $last_evaluation_time = sprintf("%02d:%02d", $minutes, $seconds);

    $nEvaluations = $overview["nEvaluations"];
    $labels = $overview["labels"];
    $answersByLabel = $overview["answersByLabel"];
    
?>

<!doctype html>
<html lang="pt-BR" style="height: 100vh">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="../../css/bootstrap/bootstrap.css">
		
		<!-- CSS Local -->
		<link rel="stylesheet" href="../../css/checklist.css">
		<link rel="stylesheet" href="../../css/checklistAnalytics.css">

    <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>

		<title><?= $checklist->get_title() ?> checklist</title>
	</head>

	<body style="height: 100vh">
		<!-- Navbar -->
    <?php include('../templates/navbar.php'); ?>
	
    <div class="analytics-container">
      <div class="page-title">
        <a href="./checklistEvaluations.php?c_id=<?= $checklist->get_id() ?>">
          <i class="fas fa-chevron-left fa-lg mr-3"></i>
        </a>
        <div class="checklist-infs">
          <h1>Checklist Analytics</h1>
          <div class="checklist-name">
            <p><?= $checklist->get_title() ?></p>
          </div>
        </div>
      </div>
      <div class="analytics-data">
        <div class="section-graphic">
          <canvas id="answers-by-section"></canvas>
        </div>
        <div class="overview-graphic chart-bg">
          <div>
            <canvas id="overview"></canvas>
          </div>
          <div class="overview-text">
            <p>
              <?= $nEvaluations ?>
            </p>
            <p>evaluations</p>
          </div>
        </div>
        <div class="items-graphic">Answers by items</div>
        <div class="info-graphic">
          <div class="info-time chart-bg">
            <p>Average Time<br/>to Evaluate</p>
            <div class="time">
              <div class="clock-icon">
                <i class="far fa-clock"></i>
              </div>
              <div class="average-time">
                <p><?= $average_time ?> <span>sec</span></p>
              </div>
            </div>
            <div class="last-evaluation">
              <p>Last Evaluation</p>
              <div>
                <div class="clock-icon">
                  <i class="far fa-clock"></i>
                </div>
                <div class="last-time">
                  <p><?= $last_evaluation_time ?> <span>sec</span></p>
                </div>
              </div>
            </div>
          </div>
          <div class="info-numbers">
            <div class="total-questions chart-bg">
              <p>Total<br/>Questions</p>
              <p><?= $infoNumbers["total_questions"] ?></p>
            </div>
            <div class="unfinished-evaluations chart-bg">
              <p>Unfinished<br/>Evaluations</p>
              <p><?= $infoNumbers["total_unfinished_evaluations"] ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>

		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS and ChartJS -->
    <script src="../../js/jquery-3.5.1.js"></script>
    <script src="../../js/popper-base.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/patternomaly/1.3.2/patternomaly.js"
      integrity="sha512-gNM40ajr/bSi3Af8i6D4dV2CUWZrkm2zhgeWf46H91zOwWoH8Wwsyf6kQ4syfNyOrnjATrjKkP4ybWD7eKp2KA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer">
    </script>
	</body>
</html>

<script type=text/javascript>

  /*
    If chart is not responsive, add min-width: 0 in the parent div of the chart
  */

  function getColors(){
    let hue = Math.floor(Math.random() * 360);
    let saturation = Math.floor(Math.random() * (70 - 50) + 50);
    let luminosity =  Math.floor(Math.random() * (70 - 60) + 60);

    colors = {
      backgroundColor: `hsl(${hue}, ${saturation}%, ${luminosity}%)`,
      hoverColor: `hsl(${hue}, ${saturation}%, ${luminosity-20}%)`
    }

    return colors;
  }

  var sections = <?= json_encode($answersBySections["sections"]) ?>;
  var labels = <?= json_encode($answersBySections["labels"]) ?>;
  var answers = <?= json_encode($answersBySections["answers"]) ?>;
  
  var datasets = [];

  sectionsNumber = sections.length;
  labelsNumber = labels.length;
  for(i=0; i<labelsNumber; i++) {
    data = [];
    for(j=0; j<sectionsNumber; j++) {
      data.push(answers[j][i]);
    }

    randomColors = getColors();

    dataset = {
      label: labels[i],
      backgroundColor: randomColors.backgroundColor,
      hoverBackgroundColor: randomColors.hoverColor,
      minBarLength: 6,
      data,
    }
    datasets.push(dataset);
  }

  var data = {
    labels: <?= json_encode($answersBySections["sections"]) ?>,
    datasets
  };

  var options = {
    categoryPercentage: 0.6,
    indexAxis: 'y',
    maintainAspectRatio: false,
    scales: {
      x: {
        stacked: true,
        grid: {
          display: false
        },
        ticks: {
          stepSize: 1,
          font: {
            size: 18
          }
        },
        min: 0,
        afterDataLimits(scale) {
          scale.max += 1;
        }
      },
      y: {
        stacked: true,
        grid: {
          display: true
        },
        ticks: {
          font: {
            size: 18
          }
        }
      },
    },
    plugins: {
      title: {
        display: true,
        text: "Number of Answers by Section",
        align: "start",
        font: {
          size: 26,
          weight: 700
        }
      },
      legend: {
        align: "end",
        labels: {
          font: {
            size: 20,
            weight: 400
          }
        }
      }
    },
  };

  const config = {
    type: 'bar',
    data,
    options
  };

  var answersBySection = new Chart(
    document.getElementById('answers-by-section'),
    config
  );

  window.addEventListener('beforeprint', () => {
    myChart.resize(600, 600);
  });
  window.addEventListener('afterprint', () => {
    myChart.resize();
  });

</script>

<script type="text/javascript">

  createOverview();

  function createOverview()
  {
    const nEvaluations = <?= $nEvaluations ?>;
    const labels = <?= json_encode($labels) ?>;
    const answersByLabel = <?= json_encode($answersByLabel) ?>;

    const labelsName = []

    labels.forEach(obj => labelsName.push(obj.text))

    const backgroundColor = []

    answersByLabel.forEach(() => {
      const color = getColors()
      backgroundColor.push(color.backgroundColor)
    })

    const datasets = [{
      label: "Overview",
      data: answersByLabel,
      backgroundColor: pattern.generate(backgroundColor),
      hoverOffset: 2
    }]

    const data = {
      labels: labelsName,
      datasets
    }

    const config = {
      type: "doughnut",
      data,
      options: {
        plugins: {
          title: {
            display: true,
            text: "Overview",
            align: "center",
            font: {
              size: 26,
              weight: 700
            }
          },
          legend: {
            labels: {
                font: {
                  size: 20,
                  weight: 400
              }
            }
          }
        }
      }
    }

    const overview = new Chart(
      document.getElementById("overview"),
      config
    )
  }

</script>