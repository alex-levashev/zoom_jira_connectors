<html>
<head>
		<title>Invoices</title>
		<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
		<link rel="shortcut icon" href="../favicon.gif" type="image/gif">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/dataTables.bootstrap.min.css" rel="stylesheet">
		<link href="css/fixedHeader.dataTables.min.css" rel="stylesheet">
		<script src="js/tether.min.js"></script>
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>
		<script src="js/dataTables.bootstrap.min.js"></script>
		<script src="js/dataTables.fixedHeader.min.js"></script>
		<script src="js/Chart.bundle.js"></script>
		<script src="js/Chart.js"></script>



</head>
<body>
	<form>
		<div class="form-group">
			<label for="startdate" class="col-sm-2 control-label">Start date</label>
			<div class="col-sm-5">
				<select type="startdate" name="startdate" class="form-control" id="startdate">
					<option>01</option>
					<option>02</option>
					<option>03</option>
					<option>04</option>
					<option>05</option>
					<option>06</option>
					<option>07</option>
					<option>08</option>
					<option>09</option>
					<option>10</option>
					<option>11</option>
					<option>12</option>
				</select>
			</div>
			<div class="col-sm-5">
				<select type="startdateyear" name="startdateyear" class="form-control" id="startdateyear">
					<option>2016</option>
					<option>2017</option>
					<option selected>2018</option>
				</select>
			</div>
		</div>
		<br><br>
		<div class="form-group">
			<label for="enddate" class="col-sm-2 control-label">End date</label>
			<div class="col-sm-5">
				<select type="enddate" name="enddate" class="form-control" id="enddate">
					<option>01</option>
					<option>02</option>
					<option>03</option>
					<option>04</option>
					<option>05</option>
					<option>06</option>
					<option>07</option>
					<option>08</option>
					<option>09</option>
					<option>10</option>
					<option>11</option>
					<option>12</option>
				</select>
			</div>
			<div class="col-sm-5">
				<select type="enddateyear" name="enddateyear" class="form-control" id="enddateyear">
					<option>2016</option>
					<option>2017</option>
					<option selected>2018</option>
				</select>
			</div>
		</div>
		<br><br>
		<center><button type="submit" class="btn btn-primary">Submit</button></center>
	</form>

	<div>
	<?php
	if (($_GET["startdate"] == '') || ($_GET["startdateyear"] == '')) {
		$FilterStartDate = date('Y-m', mktime(0, 0, 0, date('m')-1, 1, date('Y'))) . '-01';
	}
	else {
		$FilterStartDate = date('Y-m', mktime(0, 0, 0, $_GET["startdate"], 1, $_GET["startdateyear"])) . '-01';
	}


	if (($_GET["enddate"] == '') || ($_GET["enddateyear"] == '')) {
		$FilterEndDate = date('Y-m', mktime(0, 0, 0, date('m'), 1, date('Y'))) . '-01';
	}
	else {
		$FilterEndDate = date('Y-m', mktime(0, 0, 0, $_GET["enddate"], 1, $_GET["enddateyear"])) . '-01';
	}

	echo '<h3 class="text-center"><span class="label label-default">' . $FilterStartDate . '</span> ... <span class="label label-default">' . $FilterEndDate . '</span></h3><br>';

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


  MTTimeCalculation($zoom_team, $username, $password, 12, "MT Graph", "MT Hour Spread Chart", $FilterStartDate, $FilterEndDate, "35%");

  ?>

	</div>
</body>
</html>
