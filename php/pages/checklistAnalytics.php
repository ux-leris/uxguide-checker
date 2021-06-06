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

    //  Initiate curl
    $ch = curl_init();
    // Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // Set the url
    $url = "http://localhost/uxguide-checker/php/api/answersBySections.php?c_id=$checklist_id";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Accept: application/json'
    ));
    // Execute
    $result = curl_exec($ch);
    // Closing
    curl_close($ch);
    
    // Removing UTF-8 Bom 
    $result = str_replace("\xEF\xBB\xBF",'',$result); 

    // Decoding
    $answersBySections = json_decode($result, true);

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
        <div class="overview-graphic">Overview</div>
        <div class="items-graphic">Answers by items</div>
        <div class="info-graphic">
          <div class="info-time">Time</div>
          <div class="info-numbers">
            <div>Some numbers</div>
            <div>Some numbers</div>
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
  }
  .analytics-container {
    height: 85vh;
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
  .section-graphic {
    position: relative;
    width: 100%;
    height: 100%;
    min-width: 0;
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
      minBarLength: 4,
      data,
    }
    datasets.push(dataset);
  }

  var data = {
    labels: <?= json_encode($answersBySections["sections"]) ?>,
    datasets
  };

  var options = {
    maintainAspectRatio: false,
    scales: {
      y: {
        grid: {
          display: true
        },
        ticks: {
          stepSize: 1,
        },
        min: 0,
        afterDataLimits(scale) {
          scale.max += 1;
        }
      },
      x: {
        grid: {
          display: false
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