<?php

use Twig\Environment;

require_once("../classes/database.class.php");
    require_once("../classes/checklist.class.php");
    require_once("../dataAccess/evaluationDAO.class.php");
    require_once("../../enviroment.php");

    session_start();

    $baseURL = Enviroment::getBaseURL();

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
    $url = "$baseURL/php/api/answersBySections.php?c_id=$checklist_id";
    curl_setopt($ch, CURLOPT_URL, $url);

    // Get answers by sections
    $result_answers = curl_exec($ch);

    $url = "$baseURL/php/api/infoNumbers.php?c_id=$checklist_id";
    curl_setopt($ch, CURLOPT_URL, $url);
  
    // Get info numbers
    $result_infoNumbers = curl_exec($ch);

    $url = "$baseURL/php/api/overview.php?c_id=$checklist_id";
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

    <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>

		<title><?= $checklist->get_title() ?> checklist</title>
	</head>

	<body style="height: 100vh">
		<!-- Navbar -->
    <?php include('../templates/navbar.php'); ?>
	
    <div class="analytics-container">
      <div style="display: flex; align-items: center;margin-bottom: 2rem;">
        <a href="./checklistEvaluations.php?c_id=<?= $checklist->get_id() ?>" style="color:#8FAD88;"><i class="fas fa-chevron-left fa-lg mr-3"></i></a>
        <h1>Checklist Analytics</h1>
        <h6 style="margin: 0 1rem">• <?= $checklist->get_title() ?></h4>
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
        <div class="items-graphic">
          <ul class="sections-list">
            <li class="active">Section 1</li>
            <li>Section 2</li>
            <li>Section 3</li>
            <li>Section 4</li>
            <li>Section 5</li>
          </ul>
          <table>
            <thead>
              <tr>
                <th>Question</th>
                <th>Answers</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Aspectos de usabilidade</td>
                <td><div style="min-width: 0; height: 2.5rem; width: 100%"><canvas id="question-0"></canvas></div></td>
                <td><button class="btn btn-primary">Justifications</button></td>
              </tr>
              <tr>
                <td>Aspectos visuais</td>
                <td><div style="min-width: 0; height: 2.5rem; width: 100%"><canvas id="question-1"></canvas></div></td>
                <td></td>
              </tr>
              <tr>
                <td>Qualidade da informação</td>
                <td><div style="min-width: 0; height: 2.5rem; width: 100%"><canvas id="question-2"></canvas></div></td>
                <td></td>
              </tr>
              <tr>
                <td>Qualidade da informação</td>
                <td><div style="min-width: 0; height: 2.5rem; width: 100%"><canvas id="question-3"></canvas></div></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="info-graphic">
          <div class="info-time">
            <h4>Average time<br/> to evaluate</h4>
            <div class="time">
              <i class="far fa-clock" style="font-size: 2.5rem"></i>
              <h3><?= $average_time ?></h3>
              <sub style="font-size: 1.5rem">s</sub>
            </div>
            <div>
              <span>Last evaluation:</span>
              <div class="time" style="gap: 0.1rem">
                <i class="far fa-clock"></i>
                <span><?= $last_evaluation_time ?></span>
                <sub>s</sub>
              </div>
            </div>
          </div>
          <div class="info-numbers">
            <div>
              <h4>Total<br/> questions</h4>
              <h2><?= $infoNumbers["total_questions"] ?></h2>
            </div>
            <div>
              <h4>Unifinished<br/> evaluations</h4>
              <h2><?= $infoNumbers["total_unfinished_evaluations"] ?></h2>
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
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/patternomaly/1.3.2/patternomaly.js"
      integrity="sha512-gNM40ajr/bSi3Af8i6D4dV2CUWZrkm2zhgeWf46H91zOwWoH8Wwsyf6kQ4syfNyOrnjATrjKkP4ybWD7eKp2KA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer">
    </script>
	</body>
</html>

<style>
  html, body {
    font-size: 16px;
    color: #1E1E31;
  }
  .analytics-container {
    height: 80vh;
    padding: 2rem;
  }
  .analytics-data {
    display: grid;
    height: 100%;
    grid-template-columns: 1.8fr 1fr;
    grid-template-rows: 1.2fr 1fr;
    grid-gap: 3rem;
  }
  .info-graphic {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-gap: 2rem; 
  }
  .info-numbers {
    display: grid;
    grid-template-rows: 1fr 1fr;
    grid-gap: 2rem; 
  }
  .info-numbers > div {
    display: flex;
    gap: 1.5rem;
    align-items: center;
    justify-content: center;
    background-color: #EEECF5;
    border-radius: 0.6rem;
    padding: 1.5rem;
  }
  .chart-bg {
    display: flex;
    gap: 1.5rem;
    align-items: center;
    justify-content: center;
    background-color: #EEECF5;
    border-radius: 0.6rem;
    padding: 1.5rem;
  }
  .overview-text {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    font-size: 32px;
  }
  .overview-text p:first-child {
    font-weight: bold;
  }
  .items-graphic {
    display: flex;
    flex-direction: column;
    background-color: #EEECF5;
    border-radius: 0.6rem;
    padding: 1.5rem;
  }
  ul.sections-list {
    display: flex;
    flex-wrap: wrap;
    
    white-space: nowrap;

    background-color: #E3DFDF;
    border-radius: 10px 10px 0 0;

    margin: 0;
    padding: 0;
  }
  ul.sections-list > li { 
    color: #878799;

    padding: 1rem;
    list-style-type: none;
  }
  ul.sections-list > li:hover, 
  ul.sections-list > li.active {
    background-color: #007175;
    color: #F3F3FC;

    border-radius: 10px 10px 0 0;
  }
  table {
    border-collapse: separate;
    border-spacing: 0 0.2rem;
  }
  tr, thead > tr {
    background-color: #DEDEDE;
  } 
  tbody > tr:nth-child(2n+1) {
    background-color: #E8E6F0;
  }
  th, td {
    padding: 1rem;
  }
  h2 {
    font-size: 3.25rem;
    font-weight: 500;
    margin: 0;
  }
  h3 {
    font-size: 2.5rem;
    font-weight: 500;
    margin: 0;
  }
  h4 {
    font-size: 1.25rem;
    font-weight: 500;
    margin: 0;
  }
  .section-graphic {
    position: relative;
    width: 100%;
    height: 100%;
    min-width: 0;
    background-color: #EEECF5;
    border-radius: 0.6rem;
    padding: 2rem;
  }
  .info-time {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 1.5rem;
    gap: 1.5rem;
    background-color: #EEECF5;
    border-radius: 0.6rem;
  }
  .time {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

</style>

<script>

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
      },
    },
    plugins: {
      title: {
        display: true,
        text: "Number of answers by sections",
        align: "start",
        font: {
          size: 20,
          weight: 500,
        }
      },
      legend: {
        align: "end",
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
    }

    const overview = new Chart(
      document.getElementById("overview"),
      config
    )
  }

</script>

<script>

  create_answersByQuestions();

  function create_answersByQuestions() {

    Chart.defaults.set('plugins.datalabels', {
      color: '#F3F3FC',
      font: {
        weight: 'bold'
      }
    });

    const charts_datas = [[8, 10, 15], [4, 12, 4], [5, 20, 10], [16, 5, 400]];
    const charts_labels = ["Sim", "Não", "Talvez"];

	  var charts = [];

    for(let i=0; i<4; i++) {
      let datasets = [];
      
      charts_labels.forEach((label, index) => {
        let color = getColors();
        let dataset = {
          label: charts_labels[index],
          data: [charts_datas[i][index]],
          backgroundColor: color.backgroundColor,
        }
        datasets.push(dataset);
      });

      let data = {
        labels: ["Answers"],
        datasets
      };

      let config = {
        plugins: [ChartDataLabels],
        type: 'bar',
        data,
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              stacked: true,
              grid: {
                display: false
              },
              display: false,
            },
            x: {
              stacked: true,
              grid: {
                display: false
              },
              display: false,
            },
          },
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              enabled: false
            }
          }
        }
      };

      const chart = new Chart(
        document.getElementById(`question-${i}`),
        config,
      );

      charts.push(chart);
      
    }

  }



</script>