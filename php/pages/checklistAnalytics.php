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
    $url = "http://localhost/uxguide-checker/php/api/answersBySections.php?c_id=$checklist_id";
    curl_setopt($ch, CURLOPT_URL, $url);

    // Get answers by sections
    $result_answers = curl_exec($ch);

    $url = "http://localhost/uxguide-checker/php/api/infoNumbers.php?c_id=$checklist_id";
    curl_setopt($ch, CURLOPT_URL, $url);
  
    // Get info numbers
    $result_infoNumbers = curl_exec($ch);

    // Closing
    curl_close($ch);
    
    // Removing UTF-8 Bom 
    $result_answers = str_replace("\xEF\xBB\xBF",'',$result_answers); 
    $result_infoNumbers = str_replace("\xEF\xBB\xBF",'',$result_infoNumbers); 

    // Decoding
    $answersBySections = json_decode($result_answers, true);
    $infoNumbers = json_decode($result_infoNumbers, true);

    $avg = $infoNumbers['average_time'];
    $minutes = floor($avg / 60);
    $avg -= $minutes*60;
    $seconds = $avg;
    $average_time = sprintf("%02d:%02d", $minutes, $seconds);

    $last_evaluation_time = $infoNumbers['finished_evaluations'][$infoNumbers['total_finished_evaluations']-1];
    $minutes = floor($last_evaluation_time / 60);
    $last_evaluation_time -= $minutes*60;
    $seconds = $last_evaluation_time;
    $last_evaluation_time = sprintf("%02d:%02d", $minutes, $seconds);
    
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
        <div class="overview-graphic">
          <span>Overview</span>
          <span><?= $infoNumbers["total_evaluations"] ?></span>
        </div>
        <div class="items-graphic">Answers by items</div>
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
    grid-template-columns: 1.5fr 1fr;
    grid-template-rows: 1.2fr 1fr;
    grid-gap: 4rem;
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

  window.addEventListener('beforeprint', () => {
    myChart.resize(600, 600);
  });
  window.addEventListener('afterprint', () => {
    myChart.resize();
  });

</script>