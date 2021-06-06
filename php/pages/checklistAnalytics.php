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
		//$url = "http://localhost/uxguide-checker/php/api/answersBySections.php?c_id=$checklist_id";
		// Guga's URL
		$url = "http://localhost/applications/uxguide-checker/php/api/answersBySections.php?c_id=$checklist_id";
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
		<link rel="stylesheet" href="../../css/global.css">
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
				<h1>
					Checklist Analytics
					<span>
						<?= $checklist->get_title() ?>
					</span>
				</h1>
			</div>
			<div class="analytics-data">
				<div class="chart-display section-graphic">
					<canvas class="answers-by-section"></canvas>
				</div>
				<div class="chart-display overview-graphic">Overview</div>
				<div class="chart-display items-graphic">Answers by items</div>
				<div class="chart-display info-graphic">
					<div class="info-time">Time</div>
					<div class="info-numbers">
						<div class="total-questions">Some numbers</div>
						<div class="unfinished-evaluations">Some numbers</div>
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

<script>

	let previousHue = Math.round(Math.random() * 360 / 60) * 60;

	const sections = <?= json_encode($answersBySections["sections"]) ?>;
	const labels = <?= json_encode($answersBySections["labels"]) ?>;
	const answers = <?= json_encode($answersBySections["answers"]) ?>;
	
	let dataSet = []

	for(let i = 0; i < labels.length; i++)
	{
		let labelData = []

		for(let j = 0; j < sections.length; j++)
			labelData.push(answers[j][i])

		const labelColor = getRandomColor()

		const label = {
			label: labels[i],
			backgroundColor: labelColor.backgroundColor,
			hoverBackgroundColor: labelColor.hoverBackgroundColor,
			minBarLength: 4,
			data: labelData
		}

		dataSet.push(label)
	}

	const chartData = {
		labels: sections,
		datasets: dataSet
	}

	const chartOptions = {
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
			}
		},
		plugins: {
			title: {
				display: true,
				text: "Number of Answers by Sections",
				align: "start",
				font: {
					size: 18,
					weight: 400,
				}
			},
			legend: {
				align: "end",
			},
			tooltip: {
				enabled: false
			}
		}
	}

	const chartConfig = {
		type: "bar",
		data: chartData,
		options: chartOptions
	}

	const answersBySection = new Chart(
		document.querySelector(".answers-by-section"),
		chartConfig
	)

	window.addEventListener('beforeprint', () => {
		myChart.resize(600, 600);
	});
	window.addEventListener('afterprint', () => {
		myChart.resize();
	});

	function getRandomColor()
	{
		const hue = previousHue + 60 > 360 ? previousHue + 60 % 360 : previousHue + 60
		const saturation = 60
		const luminosity = 50

		colors = {
			backgroundColor: `hsl(${hue}, ${saturation}%, ${luminosity}%)`,
			hoverBackgroundColor: `hsl(${hue}, ${saturation}%, ${luminosity - 15}%)`
		}

		previousHue = hue;

		return colors
	}

</script>