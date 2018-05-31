<html>
<head>
		<title>Test Environment</title>
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

	<?php

	if (file_exists('../service/config/config.php')) {
		require_once("../service/config/config.php");
	} else {
		require_once("../config/config.php");
	}

	if (file_exists('../int/teams_rates.php')) {
		require_once("../int/teams_rates.php");
	}

	$team_members = [];
	$users = '';
	foreach ($zoom_team as $key => $value) {
		if($value['location'] == 'Out') {
			$users .= $key . ", ";
			$team_members[$key] = $value;
		}
	}
	$users = substr($users, 0, -2);
	$DateToStart = '2017-01-01';

	echo '<div class="well">Internal Teams MT Stats</div>';
	echo '<table class="table table-striped table-bordered" cellspacing="0" width="100%">';
	echo '<thead><tr>';
		echo '<th>Date Range</th>';
		echo '<th>MT issues count</th>';
		echo '<th>MT hours logged</th>';
	echo '</thead></tr>';

	echo "<tbody>";

	for ($increment = 0; $increment <= 80; $increment++) {
	// CYCLE START

		// $FilterStartDate = date('Y-m', mktime(0, 0, 0, date('m')-1, 1, date('Y'))) . '-01';
		// $FilterEndDate = date('Y-m', mktime(0, 0, 0, date('m')-0, 1, date('Y'))) . '-01';

		$StrFilterStartDate = $increment . ' week';
		$StrFilterEndDate = $increment+1 . ' week';

		$FilterStartDate = date("Y-m-d", strtotime($DateToStart . '+' . $StrFilterStartDate));
		$FilterEndDate = date("Y-m-d", strtotime($DateToStart . '+' . $StrFilterEndDate));


		$filter = 'cf[10085] not in (' . $users . ') AND status = Closed AND "Discovered During" = customer AND status changed TO "Closed" DURING (' . $FilterStartDate . ',' . $FilterEndDate . ')';
		$url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($filter) . '&maxResults=1000';


		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

		$issues = json_decode(curl_exec($curl), true);
		$count = 0;
		$time_spent = 0;

		foreach ($issues['issues'] as $issue) {
			$key = $issue['key'];
			$count += 1;
			curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key/worklog");
			$worklog = json_decode(curl_exec($curl), true);
			curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key?fields=timetracking");
			$timetracking = json_decode(curl_exec($curl), true);
			foreach ($worklog['worklogs'] as $i) {
				$startDate = substr($i['started'], 0, 10);
				$author_full = $i['author']['displayName'];
				$author_nick = $i['author']['name'];
				$logged_time = $i['timeSpentSeconds'];
				if (!array_key_exists($author_nick, $team_members) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) < strtotime($FilterEndDate)) {
					$time_spent += $logged_time/3600;
				}
			}

		}

		echo "<tr>";
			echo "<td>";
			echo '<b>' . $FilterStartDate . '</b> to <b>' . $FilterEndDate . '<b>	';
			echo "</td>";

			echo "<td>";
			echo $count;
			echo "</td>";

			echo "<td>";
			echo $time_spent . ' h';
			echo "</td>";

		echo "</tr>";

		// CYCLE END
	}

	echo "</tbody>";
	echo "</table>";



	?>

	<script>

		var table = $('#js_table').DataTable( {
				"fixedHeader": {
						header: true,
						footer: true
				},
				"pageLength": 100
		} );
	</script>

</body>
</html>
