<html>
<head>
		<title>Time per month - Interna People</title>
		<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
		<link rel="shortcut icon" href="../../favicon.gif" type="image/gif">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="../css/bootstrap.css" rel="stylesheet">
		<link href="../css/bootstrap.css" rel="stylesheet">
		<link href="../css/dataTables.bootstrap.min.css" rel="stylesheet">
		<link href="../css/fixedHeader.dataTables.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" rel="stylesheet">
		<script src="../js/tether.min.js"></script>
		<script src="../js/jquery.min.js"></script>
		<script src="../js/moment.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/bootstrap.js"></script>
		<script src="../js/jquery.dataTables.min.js"></script>
		<script src="../js/dataTables.bootstrap.min.js"></script>
		<script src="../js/dataTables.fixedHeader.min.js"></script>
		<script src="../js/Chart.bundle.js"></script>
		<script src="../js/Chart.js"></script>
		<script src="../js/transition.js"></script>
		<script src="../js/bootstrap-datetimepicker.min.js"></script>



</head>
<body>
	<div class="container col-sm-12">
		<div class="page-header">
			<h3 class="text-center">Time per month - Interna People</h3>
		</div>
	</div>
		<div class="container">
	<?php

	if (file_exists('../procedures/proc.php')) {
    require_once("../procedures/proc.php");
  } else {
    echo 'No file with procedures!';
  }
	DateTimePick();

	if (file_exists('../int/teams_rates.php')) {
		require_once("../int/teams_rates.php");
	}

		if (file_exists('../service/config/config.php')) {
			require_once("../service/config/config.php");
		} else {
			require_once("../config/config.php");
		}

		foreach ($zoom_team as $key => $value) {
			if($value['location'] == 'In') {
				$team_members_in[$key] = $value;
			}
		}

		$users = $team_members_in;


		$request = 'worklogAuthor in (' . implode(",", array_keys($team_members_in)) . ') AND worklogDate >= ' . $FilterStartDate . ' AND worklogDate <= ' . $FilterEndDate;

		$url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request) . '&maxResults=1000';

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);


		echo '<center><h5>Total team members in the list - ' . count($users) . '</h5></center>';
		echo '<table id="example" class="table table-bordered" cellspacing="0" width="100%">';
		echo '<thead><tr>';
			echo '<th>Name</th>';
			echo '<th>Team</th>';
			echo '<th>Hours</th>';
		echo '</thead></tr>';

		echo "<tbody>";
		$issues = json_decode(curl_exec($curl), true);
		foreach ($issues['issues'] as $issue) {
      $key = $issue['key'];
			curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key/worklog");
			$worklog = json_decode(curl_exec($curl), true);
			foreach ($worklog['worklogs'] as $i) {
        $startDate = substr($i['started'], 0, 10);
				$author_full = $i['author']['displayName'];
				$author_nick = $i['author']['name'];
				$logged_time = $i['timeSpentSeconds'];
        if (array_key_exists($author_nick, $users) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) <= strtotime($FilterEndDate)) {
          $users[$author_nick]['hours'] += $logged_time/3600;
					$users[$author_nick]['full_name'] = $author_full;
				}
			}
		}
    foreach ($users as $i  => $val) {
			if ($val['full_name'] != '') {
				if ($val['hours'] < 100) {
					echo '<tr class="danger">';
				}
				else {
					echo "<tr>";
				}
	        echo "<td>";
	        echo $val['full_name'];
	        echo "</td>";
	        echo "<td>";
	        echo $val['team'];
	        echo "</td>";
					echo "<td>";
	        echo round($val['hours']);
	        echo "</td>";
	      echo "</tr>";
			}
    }

    echo "</tbody>";
		echo "</table>";

	echo '</div>';
	?>
	</div>
</div>
	<script>

	$(document).ready(function(){
			$('.collapse').collapse({
  			toggle: false
			})
			$('[data-toggle="tooltip"]').tooltip({html: true});
	});
	$(document).ready(function() {

		var table = $('#example').DataTable( {
        "fixedHeader": {
            header: true,
            footer: true
        },
	"pageLength": 100
    } );

		} );

</script>
</body>
</html>
