<html>
<head>
		<title>Team per day</title>
		<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
		<link rel="shortcut icon" href="../favicon.gif" type="image/gif">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="../css/bootstrap.css" rel="stylesheet">
		<link href="../css/bootstrap.css" rel="stylesheet">
		<link href="../css/dataTables.bootstrap.min.css" rel="stylesheet">
		<link href="../css/fixedHeader.dataTables.min.css" rel="stylesheet">
		<script src="../js/tether.min.js"></script>
		<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/bootstrap.js"></script>
		<script src="../js/jquery.dataTables.min.js"></script>
		<script src="../js/dataTables.bootstrap.min.js"></script>
		<script src="../js/dataTables.fixedHeader.min.js"></script>
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
					<option selected>2017</option>
					<option>2018</option>
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
					<option selected>2017</option>
					<option>2018</option>
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

	if (file_exists('../service/config/config.php')) {
		require_once("../service/config/config.php");
	} else {
		require_once("../config/config.php");
	}

	$team = array(
		"pilgrim.aambartsumya" => "Pilgrim",
    "pilgrim.ssover" => "Pilgrim",
		"lumiere.akapelonis" => "LumierePro",
    "lumiere.ppribyl" => "LumierePro",
    "lumiere.vli" => "LumierePro",
    "ext.gbolyuba" => "LumierePro",
    "lumiere.dnguyen" => "LumierePro"
	);

	$request = 'worklogAuthor in (' . implode(",", array_keys($team)) . ') AND worklogDate >= ' . $FilterStartDate . ' AND worklogDate < ' . $FilterEndDate;
	$lumiere_total_hours = 0;
	$lumiere_total_cost = 0;

	$pilgrim_total_hours = 0;
	$pilgrim_total_cost = 0;


	$url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request) . '&maxResults=1000';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

	echo '<div class="well">Pilgrim</div>';
	echo '<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">';
	echo '<thead><tr>';
		echo '<th>Team Name</th>';
		echo '<th>Date</th>';
		echo '<th>Week day</th>';
		echo '<th>Hours logged</th>';
	echo '</thead></tr>';
	echo "<tbody>";
	echo "</tbody>";
	echo "</table>";

	// $issues = json_decode(curl_exec($curl), true);
	//
	// foreach ($issues['issues'] as $issue) {
	// 	$key = $issue['key'];
	// 	$changelog = $issue['changelog']['histories'];
	// 	curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key/worklog");
	// 		$worklog = json_decode(curl_exec($curl), true);
	// 		foreach ($worklog['worklogs'] as $i) {
	// 			$startDate = substr($i['started'], 0, 10);
	// 			$author_full = $i['author']['displayName'];
	// 			$author_nick = $i['author']['name'];
	// 			$logged_time = $i['timeSpentSeconds'];
	// 				if (array_key_exists($author_nicx, $team_members) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) < strtotime($FilterEndDate)) {
	// 					$time_spent += $logged_time/3600;
	// 				}
	// 			}
	// 		}


	?>
</body>
</html>
