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

    <div id="justifications-modal" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="class">Question Justifications</h5>
            <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
            </button>
          </div>
          <div class="modal-body"></div>
        </div>
      </div>
    </div>

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
        <section class="section-graphic section-bg">
          <i
            class="fas fa-question-circle"
            data-toggle="tooltip"
            data-placement="left"
            title="Describe the number of each option responses per section in all checklist evaluations">
          </i>
          <h4 class="chart-title">Number of Answers by Questions</h4>
          <div class="chart-view-configs">
            <canvas id="answers-by-section"></canvas>
          </div>
        </section>
        <section class="overview-graphic section-bg">
          <i
            class="fas fa-question-circle"
            data-toggle="tooltip"
            data-placement="left"
            title="Describe the number of responses per option in all checklist evaluations">
          </i>
          <h4 class="chart-title">Overview</h4>
          <div class="overview-infos">
            <div class="chart-view-configs">
              <canvas id="overview"></canvas>
            </div>
            <div class="overview-text">
              <p>
                <?= $nEvaluations ?>
              </p>
              <p>evaluations</p>
            </div>
          </div>
        </section>
        <section class="items-graphic section-bg">
          <h4 class="chart-title">Number of Answers by Questions</h4>
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
                    echo "<span class='questions-labels'><div class='label-marker'><canvas width=28 height=28 id='marker-$index'></canvas></div>".$label['text']."</span>";
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
                  <td><button class="btn btn-primary" style="height: 2.5rem;" id="<?= $key ?>" onClick="getJustifications(this.id)">Justifications</button></td>
                </tr>
              <?php $index++ ;} ?>
            </tbody>
          </table>
        </section>
        <section class="info-graphic">
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
          <section class="info-numbers">
            <div class="total-questions chart-bg">
              <i
                class="fas fa-question-circle"
                data-toggle="tooltip"
                data-placement="left"
                title="Describe the number of questions of checklist">
              </i>
              <p>Total of<br/>Questions</p>
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
          </section>
        </section>
      </div>
    </div>

		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS, ChartJS, DataLabels and PatternomalyJS -->
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
    .overview-infos {
      flex-direction: column;
      align-items: center;
    }
  }


</style>

<script> 

  Chart.defaults.font.size = 16;

  Chart.defaults.set('plugins.datalabels', {
    color: '#F3F3FC',
    backgroundColor: 'black',
    borderRadius: 6,
    padding: 5,
    font: {
      size: 16,
      weight: '600'
    },
    display: function(context) {
      var index = context.dataIndex;
      var value = context.dataset.data[index];
      return value > 0; 
    }
  });

  const mediaQuery = window.matchMedia('(max-width: 690px)')
  if (mediaQuery.matches) {
    Chart.defaults.set('plugins.datalabels', {
      padding: 1,
      borderRadius: 2,
    });
  }

  Chart.defaults.set('plugins', {
    legend: {
      labels: {
        usePointStyle: true,
      }
    },
  });

</script>

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
        data,
      };

      datasets.push(dataset);    
    })

    const data = {
      labels: <?= json_encode($answersBySections["sections"]) ?>,
      datasets
    };

    const options = {
      categoryPercentage: 0.7,
      indexAxis: 'y',
      maintainAspectRatio: false,
      scales: {
        x: {
          display: false,
          stacked: true,
          grid: {
            display: false
          },
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
          display: false,
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
      plugins: [ChartDataLabels],
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
      plugins: [ChartDataLabels],
      type: "doughnut",
      data,
      options: {
        maintainAspectRatio: false,
        plugins: {
          title: {
            display: false,
          },
          legend: {
            labels: {
                font: {
                  size: 20,
                  weight: 400
                },
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
          categoryPercentage: 1,
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
              <td><button class="btn btn-primary" style="height: 2.5rem;" id="${answer[0]}" onClick="getJustifications(this.id)">Justifications</button></td>
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

<script>
  const colors = <?= json_encode(getColors()) ?>;
  const patterns = <?= json_encode(getPatterns()) ?>;
  const labels_count = <?= $labels_count ?>;

  for(let i=0; i<labels_count; i++) {
    var canvas = document.getElementById(`marker-${i}`);
    var ctx = canvas.getContext('2d');
    ctx.fillStyle = pattern.draw(patterns[i], colors[i]);
    ctx.fillRect(0, 0, canvas.width, canvas.height);
  }

</script>

<script type="text/javascript">

  $(function() {
    $('[data-toggle="tooltip"]').tooltip();
  })

</script>

<script type="text/javascript">

  function getJustifications(questionId)
  {
    const justifications = [];

    $.ajax({
      type: "GET",
      url: "../controllers/select_justifications.php",
      data: {
        i_id: questionId
      },
      success: function(res) {
        const rows = eval(res);
        rows.forEach(row => justifications.push(row[4]));

        showJustifications(justifications);
      }
    })
  }

  function showJustifications(justifications)
  {
    let justificationsModalBody = document.querySelector('#justifications-modal .modal-body');

    justificationsModalBody.innerHTML = "";

    justifications.forEach((justification, i) => {
      justificationsModalBody.innerHTML += `
        <div class="card mb-3">
          <div class="card-body">${justification}</div>
        </div>
      `;
    })

    $('#justifications-modal').modal('show');
  }

</script>