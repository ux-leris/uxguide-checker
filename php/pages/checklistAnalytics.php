<?php

    require_once("../classes/database.class.php");
    require_once("../classes/checklist.class.php");
    require_once("../dataAccess/evaluationDAO.class.php");
    require_once("../dataAccess/chartColors.php");
    require_once("../dataAccess/chartPatterns.php");
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
    
    $evaluationDAO = new EvaluationDAO;
    $evaluationResult = $evaluationDAO->select_evaluationsOfChecklist($conn, $checklist_id, $_SESSION["USER_ID"]);
    
    if($evaluationResult->num_rows <= 0) {
      echo "<h1 style='height: 100vh; display: flex; justify-content: center; align-items: center;'>Your checklist doensn't have evaluations.<h1>";
      exit();
    }

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
    
    // Removing UTF-8 Bom 
    $result_answers = str_replace("\xEF\xBB\xBF",'',$result_answers); 
    $result_infoNumbers = str_replace("\xEF\xBB\xBF",'',$result_infoNumbers); 

    $resultOverview = str_replace("\xEF\xBB\xBF", "", $resultOverview); 

    // Decoding
    $answersBySections = json_decode($result_answers, true);
    $infoNumbers = json_decode($result_infoNumbers, true);
    $overview = json_decode($resultOverview, true);

    $first_section = $answersBySections['sections_ids'][0];
    $labels_count = sizeof($answersBySections["labels"]);
    $sections_count =  sizeof($answersBySections["sections"]);

    $url = "$baseURL/php/api/answersByQuestions.php?section_id=$first_section&labels_number=$labels_count&c_id=$checklist_id";

    curl_setopt($ch, CURLOPT_URL, $url);
  
    // Get answers by questions (first section)
    $result_answersByQuestions = curl_exec($ch);

    // Closing
    curl_close($ch);

    // Removing UTF-8 Bom 
    $result_answersByQuestions = str_replace("\xEF\xBB\xBF",'',$result_answersByQuestions); 
    // Decoding
    $answersByQuestions = json_decode($result_answersByQuestions, true);

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
          <i
            class="fas fa-question-circle"
            data-toggle="tooltip"
            data-placement="left"
            title="Describe the number of each option responses per section in all checklist evaluations">
          </i>
          <canvas id="answers-by-section"></canvas>
        </div>
        <div class="overview-graphic chart-bg">
          <i
            class="fas fa-question-circle"
            data-toggle="tooltip"
            data-placement="left"
            title="Describe the number of responses per option in all checklist evaluations">
          </i>
          <div class="overview-chart">
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
          <i
            id="help-icon"
            class="fas fa-question-circle"
            data-toggle="tooltip"
            data-placement="left"
            title="Describe the number of responses per option by section questions in all checklist evaluations">
          </i>
          <ul class="sections-list" id="sections-list">
            <?php for($i=0; $i<$sections_count; $i++) { ?>
              <li id="<?= $answersBySections["sections_ids"][$i]?>" onclick="changeSection(event)"><?= $answersBySections["sections"][$i] ?></li>
            <?php } ?>
          </ul>
          <table>
            <thead>
              <tr>
                <th>Question</th>
                <th>
                  Answers 
                  <?php $index = 0; foreach($labels as $label) { 
                    echo "<span class='questions-labels'><div class='label-marker' id='marker-$index'></div>".$label['text']."</span>";
                    $index++;
                  }?>
                </th>
              </tr>
            </thead>
            <tbody>
              <?php $index = 0; foreach($answersByQuestions["questions_answers"] as $key => $value) { ?>
                <tr>
                  <td><?= $value["title"] ?></td>
                  <td><div style="position: relative; min-width: 0; height: 2.5rem; width: 100%"><canvas id="question-<?= $index ?>"></canvas></div></td>
                  <td><button class="btn btn-primary" style="height: 2.5rem;" id="<?= $key ?>">Justifications</button></td>
                </tr>
              <?php $index++ ;} ?>
            </tbody>
          </table>
        </div>
        <div class="info-graphic">
          <div class="info-time chart-bg">
            <i
              id="help-icon"
              class="fas fa-question-circle"
              data-toggle="tooltip"
              data-placement="left"
              title="Describe the response average time of checklist and the time taken in the last response">
            </i>
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
              <i
                class="fas fa-question-circle"
                data-toggle="tooltip"
                data-placement="left"
                title="Describe the number of questions of checklist">
              </i>
              <p>Total<br/>Questions</p>
              <p><?= $infoNumbers["total_questions"] ?></p>
            </div>
            <div class="unfinished-evaluations chart-bg">
              <i
                class="fas fa-question-circle"
                data-toggle="tooltip"
                data-placement="left"
                title="Describe the number of unfinished evaluations of checklist">
              </i>
              <p>Unfinished<br/>Evaluations</p>
              <p><?= $infoNumbers["total_unfinished_evaluations"] ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>

		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS, ChartJS and PatternomalyJS -->
    <script src="../../js/jquery-3.5.1.js"></script>
    <script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>

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

    font-size: 2rem;
  }
  .overview-text p:first-child {
    font-weight: bold;
  }
  .overview-chart {
    position: relative; 
    min-width: 0; 
    height: 20rem;
  }
  .items-graphic {
    display: flex;
    flex-direction: column;
    background-color: #EEECF5;
    border-radius: 0.6rem;
    padding: 1.5rem;
    padding-top: 3.5rem; /*Sofrerá alteração na inserção do título*/

    position: relative;
  }
  .items-graphic i {
    position: absolute;
    top: 15px;
    right: 15px;

    font-size: 24px;
    color: #666;
  }
  ul.sections-list {
    display: flex;
    flex-wrap: wrap;
    
    white-space: nowrap;

    background-color: #E3DFDF;
    border-radius: 10px 10px 0 0;

    margin: 0;
    padding: 0;

    cursor: pointer;
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
    display: flex;
    align-items: center;
  }
  tr > td:nth-child(2) {
    min-width: 0;
    flex: 1;
  }
  tr > th:nth-child(2) {
    min-width: 0;
    flex: 1.5;
    display: flex;
    flex-wrap: wrap;
  }
  tr > td:nth-child(3) {
    display: flex;
    justify-content: center;
  }
  tbody > tr:nth-child(2n+1) {
    background-color: #E8E6F0;
  }
  td {
    padding: 1rem;
    flex: 0.5;
  }
  th {
    padding: 1rem;
    flex: 0.48;
  }
  .label-marker {
    height: 1rem;
    width: 1rem;
    border-radius: 360px;
    background-color: red;
    align-self: center;
    margin: 0 0.2rem 0 1rem;
  }
  .questions-labels {
    display: flex;
    align-items: center;
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

    position: relative;
  }
  .section-graphic i {
    position: absolute;
    top: 15px;
    right: 15px;

    font-size: 24px;
    color: #666;
  }

  .overview-graphic {
    width: 100%;
    height: 100%;

    position: relative;
  }
  .overview-graphic i {
    position: absolute;
    top: 15px;
    right: 15px;

    font-size: 24px;
    color: #666;
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

<style>

  @media(max-width: 1200px) {
    html, body {
      font-size: 14px;
    }
    .analytics-data {
      grid-template-columns: 1fr;
      grid-template-rows: 1fr 1fr 1fr 1fr;
    } 
  }
  @media(max-width: 690px) {
    html, body {
      font-size: 12px;
    }
    .analytics-container {
      padding: 0.5rem;
    }
    .chart-bg {
      flex-direction: column;
      padding: 1.5rem 0;
    }
    .items-graphic {
      padding: 1rem;
    }
    .info-numbers > div {
      padding: 1.5rem 0.5rem;
    }
    .info-time {
      padding: 1.5rem 0.5rem;
      align-items: center;
    }
  }


</style>

<script type="text/javascript">

  createAnswersBySection();

  function createAnswersBySection()
  {
    const sections = <?= json_encode($answersBySections["sections"]) ?>;
    const labels = <?= json_encode($answersBySections["labels"]) ?>;
    const answers = <?= json_encode($answersBySections["answers"]) ?>;
    
    const datasets = [];

    const colors = <?= json_encode(getColors()) ?>;
    const patterns = <?= json_encode(getPatterns()) ?>;

    labels.forEach((label, i) => {
      const data = [];

      sections.forEach((section, j) => {
        data.push(answers[i][j]);
      })

      const dataset = {
        label: labels[i],
        backgroundColor: pattern.draw(patterns[i], colors[i]),
        minBarLength: 6,
        data,
      };

      datasets.push(dataset);    
    })

    const data = {
      labels: <?= json_encode($answersBySections["sections"]) ?>,
      datasets
    };

    const options = {
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
  }

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
    const backgroundPatterns = []

    const colors = <?= json_encode(getColors()) ?>;
    const patterns = <?= json_encode(getPatterns()) ?>;

    answersByLabel.forEach((label, i) => {
      backgroundColor.push(pattern.draw(patterns[i], colors[i]))
    })

    const datasets = [{
      label: "Overview",
      data: answersByLabel,
      backgroundColor,
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
        maintainAspectRatio: false,
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

<script type="text/javascript">

  var question_charts = [];
  var answersByQuestions = <?= json_encode($answersByQuestions["questions_answers"]) ?>;

  create_answersByQuestions(answersByQuestions);

  function create_answersByQuestions(answersByQuestions) {

    var questionsNumber = Object.keys(answersByQuestions).length;

    Chart.defaults.set('plugins.datalabels', {
      color: '#F3F3FC',
      font: {
        weight: 'bold'
      },
      display: function(context) {
        var index = context.dataIndex;
        var value = context.dataset.data[index];
        return value > 0; 
      }
    });

    const charts_datas = [];

    for(answer of Object.values(answersByQuestions)) {
      charts_datas.push(answer['count']);
    };
    
    const charts_labels = <?= json_encode($answersBySections["labels"]) ?>;;

    const colors = <?= json_encode(getColors()) ?>;
    const patterns = <?= json_encode(getPatterns()) ?>;

    for(let i=0; i<questionsNumber; i++) {
      let datasets = [];
      
      charts_labels.forEach((label, index) => {
        let dataset = {
          label: charts_labels[index],
          data: [charts_datas[i][index]],
          backgroundColor: pattern.draw(patterns[index], colors[index]),
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

      question_charts.push(chart);
    }
  }

</script>

<script type="text/javascript"> 
  
  function changeSection(e) {
    sectionsList.forEach((element) => {
      if(e.target === element) {
        element.classList.add("active");
      } else {
        element.classList.remove("active");
      }
    });

    question_charts.forEach((chart) => {
      chart.destroy();
    });

    $.ajax({
      type: "GET",
      url: `../api/answersByQuestions.php?section_id=${e.target.id}&labels_number=<?= $labels_count ?>&c_id=<?= $checklist_id ?>`,
      success: function(data) {
        let answersByQuestions = data["questions_answers"];
        let index = 0;
        let items = '';

        for(answer of Object.entries(answersByQuestions)) {
          items += `
            <tr>
              <td>${answer[1]["title"]}</td>
              <td><div style="position: relative; min-width: 0; height: 2.5rem; width: 100%"><canvas id="question-${index}"></canvas></div></td>
              <td><button class="btn btn-primary" style="height: 2.5rem;" id="${answer[0]}">Justifications</button></td>
            </tr>
          `;
          index++;
        };

        $('tbody').html(items);

        create_answersByQuestions(answersByQuestions);
      },
    });

  }

  var sectionsList = document.querySelectorAll('#sections-list > li');

  sectionsList = Array.from(sectionsList);
  sectionsList[0].classList.add("active");

</script>

<script type="text/javascript">

  $(function() {
    $('[data-toggle="tooltip"]').tooltip();
  })

</script>