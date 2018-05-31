<html>
<head>
		<title>Maintenance Time Split</title>
		<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
		<link rel="shortcut icon" href="favicon.gif" type="image/gif">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/dataTables.bootstrap.min.css" rel="stylesheet">
		<link href="css/fixedHeader.dataTables.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" rel="stylesheet">
		<script src="js/tether.min.js"></script>
		<script src="js/jquery.min.js"></script>
		<script src="js/moment.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>
		<script src="js/dataTables.bootstrap.min.js"></script>
		<script src="js/dataTables.fixedHeader.min.js"></script>
		<script src="js/Chart.bundle.js"></script>
		<script src="js/Chart.js"></script>
		<script src="js/transition.js"></script>
		<script src="js/bootstrap-datetimepicker.min.js"></script>



</head>
<body>
	<div class="container col-sm-12">
		<div class="page-header">
			<h3 class="text-center">Maintenance Time Split</h3>
		</div>
	</div>
	<br>
	<?php

	if (file_exists('service/config/config.php')) {
		require_once("service/config/config.php");
	} else {
		require_once("config/config.php");
	}

  if (file_exists('procedures/proc.php')) {
    require_once("procedures/proc.php");
  } else {
    echo 'No file with procedures!';
  }

  if (file_exists('int/teams_rates.php')) {
    require_once("int/teams_rates.php");
  } else {
    echo 'No file with rates!';
  }

	DateTimePick();

	echo '<div class="container">';
  MTTimeCalculation($zoom_team, $username, $password, 12, "MT Graph", "MT Pie", "MT Hour Spread Chart", $FilterStartDate, $FilterEndDate, "50%");
	echo '</div>';
  ?>

	</div>
</body>
</html>
