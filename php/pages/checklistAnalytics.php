<?php

    require_once("../classes/database.class.php");
    require_once("../classes/checklist.class.php");
    require_once("../dataAccess/evaluationDAO.class.php");
    require_once("../dataAccess/labelDAO.class.php");
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

    $checklist = new Checklist($conn, $checklist_id);
    
    $evaluationDAO = new EvaluationDAO;
    $evaluationResult = EvaluationDAO::getEvaluationsOfChecklistByUser($conn, $checklist_id, $_SESSION["USER_ID"]);

    $labelDAO = new LabelDAO;
    
    if($evaluationResult->num_rows <= 0) {
      echo "<h1 style='height: 100vh; display: flex; justify-content: center; align-items: center;'>Your checklist doensn't have evaluations.<h1>";
      exit();
    }

    if(!$checklist->getId() || $checklist->getAuthorId() != $_SESSION["USER_ID"]) {
      header("HTTP/1.0 401 Unauthorized");
      echo "<h1>401 Unauthorized</h1>";
      echo "You don't have permission to access this page.";
      exit();
    }

    // Get charts datas
    $curl = initCurl();

    $answersBySections = getAnswersBySectionsData($baseURL, $curl, $checklist_id);
    $overview = getOverviewData($baseURL, $curl, $checklist_id);
    $infoNumbers = getBigNumbers($baseURL, $curl, $checklist_id);

    $first_section = $answersBySections['sections_ids'][0];
    $labels_count = sizeof($answersBySections["labels"]);

    $answersByQuestions = getAnswersByQuestionsData($baseURL, $curl, $checklist_id, $first_section, $labels_count);

    closeCurl($curl);

    $sections_count =  sizeof($answersBySections["sections"]);

    $average_time = formatEvaluationTime($infoNumbers['average_time']);

    if($infoNumbers['total_finished_evaluations'] > 0) {
      $last_evaluation_time = $infoNumbers['finished_evaluations'][$infoNumbers['total_finished_evaluations']-1];
    } else {
      $last_evaluation_time = 0;
    }
    
    $last_evaluation_time = formatEvaluationTime($last_evaluation_time);

    $numberOfEvaluations = $overview["nEvaluations"];
    $labels = $overview["labels"];
    $answersByLabel = $overview["answersByLabel"];

    $hasLabelWithJustification = false;
    $justifiableLabels = [];
    $i=0;
    foreach($labels as $label) {
      if($label["hasJustification"] == true) {
        $hasLabelWithJustification = true;
        array_push($justifiableLabels, $i);
      }
      $i++;
    }
    
?>

<?php 

    function initCurl() {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPGET, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json'
      ));

      return $ch;
    }

    function closeCurl($curl) {
      // Closing
      curl_close($curl);
    }

    function getAnswersBySectionsData($baseURL, $curl, $checklist_id) {
      // Set the url
      $url = "$baseURL/php/api/answersBySections.php?c_id=$checklist_id";
      curl_setopt($curl, CURLOPT_URL, $url);

      // Get answers by sections
      $result_answers = curl_exec($curl);

      // Removing UTF-8 Bom 
      $result_answers = str_replace("\xEF\xBB\xBF",'',$result_answers); 

      // Decoding
      $answersBySections = json_decode($result_answers, true);

      return $answersBySections;
    }

    function getOverviewData($baseURL, $curl, $checklist_id) {
      $url = "$baseURL/php/api/overview.php?c_id=$checklist_id";
      curl_setopt($curl, CURLOPT_URL, $url);
  
      $resultOverview = curl_exec($curl);
      
      // Removing UTF-8 Bom 
      $resultOverview = str_replace("\xEF\xBB\xBF", "", $resultOverview); 
  
      // Decoding
      $overview = json_decode($resultOverview, true);

      return $overview;
    }


    function getAnswersByQuestionsData($baseURL, $curl, $checklist_id, $first_section, $labels_count) {
      $url = "$baseURL/php/api/answersByQuestions.php?section_id=$first_section&labels_number=$labels_count&c_id=$checklist_id";

      curl_setopt($curl, CURLOPT_URL, $url);
    
      // Get answers by questions (first section)
      $result_answersByQuestions = curl_exec($curl);
  
      // Removing UTF-8 Bom 
      $result_answersByQuestions = str_replace("\xEF\xBB\xBF",'',$result_answersByQuestions); 

      // Decoding
      $answersByQuestions = json_decode($result_answersByQuestions, true);

      return $answersByQuestions;
    }

    function getBigNumbers($baseURL, $curl, $checklist_id) {
      $url = "$baseURL/php/api/infoNumbers.php?c_id=$checklist_id";
      curl_setopt($curl, CURLOPT_URL, $url);
    
      // Get info numbers
      $result_infoNumbers = curl_exec($curl);

      // Removing UTF-8 Bom 
      $result_infoNumbers = str_replace("\xEF\xBB\xBF",'',$result_infoNumbers); 
      
      // Decoding
      $infoNumbers = json_decode($result_infoNumbers, true);

      return $infoNumbers;
    }

    function formatEvaluationTime($averageTimeInSeconds) {
      $minutes = floor($averageTimeInSeconds / 60);
      $averageTimeInSeconds -= $minutes*60;
      $seconds = $averageTimeInSeconds;
      $average_time = sprintf("%02d:%02d", $minutes, $seconds);

      return $average_time;
    }

?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../css/bootstrap/bootstrap.css">

    <!-- CSS Local -->
    <link rel="stylesheet" href="../../css/styles/global.css">
    <link rel="stylesheet" href="../../css/styles/checklistAnalytics.css">

    <title>Checklist Analytics</title>
  </head>

	<body>

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
    <?php require_once("../templates/navbar.php"); ?>
	
    <div class="analytics-container">
      <div class="page-title">
        <a href="./checklistEvaluations.php?c_id=<?= $checklist->getId() ?>">
          <i class="fas fa-chevron-left"></i>
        </a>
        <div class="checklist-infs">
          <h1>Checklist Analytics</h1>
          <div class="checklist-name">
            <p><?= htmlspecialchars($checklist->getTitle()) ?></p>
          </div>
        </div>
      </div>
      <section class="sections-title section-bg">
        <h4 class="chart-title">Checklist sections</h4>
        <i
          id="help-icon"
          class="fas fa-question-circle"
          data-toggle="tooltip"
          data-placement="left"
          title="These are all checklist sections descriptions">
        </i>
        <ul>
          <?php for($i=0; $i<$sections_count; $i++) { ?>
            <li><b>Section <?= $i+1 ?></b> - <?= htmlspecialchars($answersBySections["sections"][$i]) ?></li>
          <?php } ?>
        </ul>
      </section>
      <div class="analytics-data">
        <section class="section-graphic section-bg">
          <i
            class="fas fa-question-circle"
            data-toggle="tooltip"
            data-placement="left"
            title="Describes the number of each response option per section in all checklist evaluations">
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
            title="Describes the total number of each response option in all checklist evaluations">
          </i>
          <h4 class="chart-title">Overview</h4>
          <div class="overview-infos">
            <div class="chart-view-configs">
              <canvas id="overview"></canvas>
            </div>
            <div class="overview-text">
              <p>
                <?= $numberOfEvaluations ?>
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
            title="Describes the number of each response option per section and question in all checklist evaluations">
          </i>
          <ul class="sections-list" id="sections-list">
            <?php for($i=0; $i<$sections_count; $i++) { ?>
              <li id="<?= $answersBySections["sections_ids"][$i]?>" onclick="changeSection(event)">Section <?= $i+1 ?></li>
            <?php } ?>
          </ul>
          <table>
            <thead>
              <tr>
                <th>Question</th>
                <th style="<?= $hasLabelWithJustification ? "flex: 1.5" : "flex: 1" ?>">
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
                  <td><?= htmlspecialchars($value["title"]) ?></td>
                  <td><div style="position: relative; min-width: 0; height: 2.5rem; width: 100%"><canvas id="question-<?= $index ?>"></canvas></div></td>
                  <?php if($hasLabelWithJustification) {
                    $hasJustification = false;
                    foreach($justifiableLabels as $justifiableLabel) {
                      if($value["count"][$justifiableLabel] > 0) {
                        $hasJustification = true;
                      }  
                    }
                  ?>
                    <?php if($hasJustification) { ?>
                      <td><button class="btn btn-primary" style="height: 2.5rem;" id="<?= $key ?>" onClick="getJustifications(this.id)">Justifications</button></td>
                    <?php } else { ?>
                      <td></td>
                    <?php } ?>
                  <?php } ?>
                </tr>
              <?php $index++; } ?>
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
              title="Describes the average time spent to finish an evaluation and the time spent in the last evaluation">
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
                title="Describes the total number of questions in the checklist">
              </i>
              <p>Total of<br/>Questions</p>
              <p><?= $infoNumbers["total_questions"] ?></p>
            </div>
            <div class="unfinished-evaluations chart-bg">
              <i
                class="fas fa-question-circle"
                data-toggle="tooltip"
                data-placement="left"
                title="Describes the number of unfinished evaluations in the checklist">
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
    <script src="../../js/bootstrap/bootstrap.js"></script>
    <script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/bc2cf3ace6.js" crossorigin="anonymous"></script>

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

  // Global configurations for the charts

  Chart.defaults.font.size = 16;
  Chart.defaults.maintainAspectRatio = false;
  Chart.defaults.set('plugins', {
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
      },
      labels: {
        usePointStyle: true,
      },
    },
    datalabels: {
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
    }
  });

  // Resize data labels for smaller screens
  const mediaQuery = window.matchMedia('(max-width: 690px)')
  if (mediaQuery.matches) {
    Chart.defaults.set('plugins.datalabels', {
      padding: 1,
      borderRadius: 2,
    });
  }
  
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
        data.push(answers[j][i]);
      })

      const dataset = {
        label: labels[i],
        backgroundColor: pattern.draw(patterns[i], colors[i]),
        data,
        maxBarThickness: 45,
      };

      datasets.push(dataset);    
    });

    const numberOfSections = sections.length;
    const defaultTicks = [];
    for(i=0; i< numberOfSections; i++) {
      defaultTicks.push(`Section ${i+1}`);
    }

    // const sectionsWithLineBreaks = [];

    // for(i=0; i<numberOfSections; i++) {
    //   let section_line = ""
    //   let section = [];
    //   let j=0;
    //   let numberOfChar = sections[i].length;
    //   while(j < numberOfChar) {
    //     section_line += sections[i][j];
    //     j++;
    //     if(j % 120 == 0 || j == numberOfChar-1) {
    //       while(sections[i][j] != " " && j < numberOfChar) {
    //         section_line += sections[i][j];
    //         j++;
    //       }
    //       j++;
    //       section.push(section_line);
    //       section_line = "";    
    //     }
    //   }
    //   sectionsWithLineBreaks.push(section);
    // }
    // console.log(sectionsWithLineBreaks);

    const data = {
      labels: defaultTicks,
      datasets
    };

    const config = {
      plugins: [ChartDataLabels],
      type: 'bar',
      data,
      options: {
        indexAxis: 'y',
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
              display: false
            },
          },
        },
      }
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
    const numberOfEvaluations = <?= $numberOfEvaluations ?>;
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
        let hasLabelWithJustification = <?php echo json_encode($hasLabelWithJustification) ?>;
        let justifiableLabels = <?php echo json_encode($justifiableLabels) ?>;

        for(answer of Object.entries(answersByQuestions)) {
          items += `
            <tr>
              <td>${answer[1]["title"]}</td>
              <td><div style="position: relative; min-width: 0; height: 2.5rem; width: 100%"><canvas id="question-${index}"></canvas></div></td>
          `;

          if(hasLabelWithJustification) {
            let hasJustification = false;
            justifiableLabels.forEach((justifiableLabel) => {
              if(answer[1]["count"][justifiableLabel] > 0) {
                hasJustification = true;
              }  
            });

            if(hasJustification) {
              items += `
                <td><button class="btn btn-primary" style="height: 2.5rem;" id="${answer[0]}" onClick="getJustifications(this.id)">Justifications</button></td>
              `;
            } else {
              items += `
                <td></td>
              `;
            }
          }

          items += `
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
        const justifications = eval(res);
        showJustifications(justifications);
      }
    })
  }

  function showJustifications(justifications)
  {
    let justificationsModalBody = document.querySelector('#justifications-modal .modal-body');

    justificationsModalBody.innerHTML = "";

    console.log(justifications);

    if(justifications.length === 0) {
      justificationsModalBody.innerHTML += `
        <p style="padding: 2rem;">No justifications.</p>
      `;
    }

    justifications.forEach(justification => {
      justificationsModalBody.innerHTML += `
        <div class="card mb-3">
          <div class="card-header">${justification.label}</div>
          <div class="card-body">${justification.text}</div>
        </div>
      `;
    })

    $('#justifications-modal').modal('show');
  }

</script>