<html>
<head>
		<title>Delays between logged start of logging and log creation</title>
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
			<h3 class="text-center">Delays between logged start of logging and log creation</h3>
		</div>
	<?php


	if (file_exists('../int/teams_rates.php')) {
		require_once("../int/teams_rates.php");
	}


	if (file_exists('../procedures/proc.php')) {
    require_once("../procedures/proc.php");
  } else {
    echo 'No file with procedures!';
  }
	
	DateTimePick();

	echo '<ul class="nav nav-tabs nav-justified nav-pills">';
		echo '<li class="active"><a data-toggle="tab" href="#scopic">Scopic<span id="badge_scopic" class="badge">' . $issues_count_scopic . '</span></a></li>';
		echo '<li><a data-toggle="tab" href="#lumiere">Lumiere<span id="badge_lumiere" class="badge">' . $issues_count_lumiere . '</span></a></li>';
		echo '<li><a data-toggle="tab" href="#pilgrim">Pilgrim<span id="badge_pilgrim" class="badge">' . $issues_count_pilgrim . '</span></a></li>';
		echo '<li><a data-toggle="tab" href="#blueberry">Blueberry<span id="badge_blueberry" class="badge">' . $issues_count_blueberry . '</span></a></li>';
	echo '</ul>';

	echo '<div class="tab-content">';

	if (file_exists('../service/config/config.php')) {
		require_once("../service/config/config.php");
	} else {
		require_once("../config/config.php");
	}

	foreach ($zoom_team as $key => $value) {
		if($value['team'] == 'Scopic') {
			$team_members_scopic[$key] = $value;
		} elseif ($value['team'] == 'LumierePro') {
			$team_members_lumiere[$key] = $value;
		} elseif ($value['team'] == 'Pilgrim') {
			$team_members_pilgrim[$key] = $value;
		} elseif ($value['team'] == 'Blueberry') {
			$team_members_blueberry[$key] = $value;
		}
	}


		$request_lumiere = 'worklogAuthor in (' . implode(",", array_keys($team_members_lumiere)) . ') AND worklogDate >= ' . $FilterStartDate . ' AND worklogDate < ' . $FilterEndDate;

		$issues_count_lumiere = 0;

		$request_scopic = 'worklogAuthor in (' . implode(",", array_keys($team_members_scopic)) . ') AND worklogDate >= ' . $FilterStartDate . ' AND worklogDate < ' . $FilterEndDate;

		$issues_count_scopic = 0;

		$request_pilgrim = 'worklogAuthor in (' . implode(",", array_keys($team_members_pilgrim)) . ') AND worklogDate >= ' . $FilterStartDate . ' AND worklogDate < ' . $FilterEndDate;

		$issues_count_pilgrim = 0;

		$request_blueberry = 'worklogAuthor in (' . implode(",", array_keys($team_members_blueberry)) . ') AND worklogDate >= ' . $FilterStartDate . ' AND worklogDate < ' . $FilterEndDate;

		$issues_count_blueberry = 0;


// ************   SCOPIC STATS
		echo '<div id="scopic" class="tab-pane fade in active">';
		$url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request_scopic) . '&maxResults=1000';
		$team_members = $team_members_scopic;
		// print_r($team_members_scopic);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);



		echo '<div class="well">Scopic Software</div>';
		echo '<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">';
		echo '<thead><tr>';
			echo '<th>Issue</th>';
			echo '<th>Author</th>';
			echo '<th>Difference</th>';
			echo '<th>Creation Time</th>';
			echo '<th>Update Time</th>';
			echo '<th>Logged Time</th>';
		echo '</thead></tr>';

		echo "<tbody>";

		$issues = json_decode(curl_exec($curl), true);
		foreach ($issues['issues'] as $issue) {
			$key = $issue['key'];
			curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key/worklog");
			$worklog = json_decode(curl_exec($curl), true);
			foreach ($worklog['worklogs'] as $i) {
				$startDate = substr($i['started'], 0, 10);
				$updateDate = substr($i['updated'], 0, 10);
				$author_full = $i['author']['displayName'];
				$author_nick = $i['author']['name'];
				$logged_time = $i['timeSpent'];
				$comment = $i['comment'];
				if (array_key_exists($author_nick, $team_members) && ((strtotime($updateDate)-strtotime($startDate))>172800) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) < strtotime($FilterEndDate))   {
					$issues_count_scopic += 1;
					echo "<tr>";
						echo "<td>";
						echo $key;
						echo "</td>";
						echo "<td>";
						echo $author_full;
						echo '</td>';
						echo "<td>";
						echo (strtotime($updateDate)-strtotime($startDate))/3600/24;
						echo ' days';
						echo '</td>';
						echo "<td>";
						echo $startDate;
						echo '</td>';
						echo "<td>";
						echo $updateDate;
						echo '</td>';
						echo "<td>";
						echo '<a data-toggle="tooltip" title="' . $comment . '">' . $logged_time . '</a>';
						echo '</td>';
					echo "</tr>";
				}
			}
		}
			echo "</tbody>";
			echo "</table>";
			echo '</div>';

// ************   PILGRIM STATS
			echo '<div id="pilgrim" class="tab-pane fade">';
			$url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request_pilgrim) . '&maxResults=1000';
			$team_members = $team_members_pilgrim;

			curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

			echo '<div class="well">Pilgrim</div>';
			echo '<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">';
			echo '<thead><tr>';
				echo '<th>Issue</th>';
				echo '<th>Author</th>';
				echo '<th>Difference</th>';
				echo '<th>Creation Time</th>';
				echo '<th>Update Time</th>';
				echo '<th>Logged Time</th>';
			echo '</thead></tr>';

			echo "<tbody>";

			$issues = json_decode(curl_exec($curl), true);
			foreach ($issues['issues'] as $issue) {
				$key = $issue['key'];
				curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key/worklog");
				$worklog = json_decode(curl_exec($curl), true);
				foreach ($worklog['worklogs'] as $i) {
					$startDate = substr($i['started'], 0, 10);
					$updateDate = substr($i['updated'], 0, 10);
					$author_full = $i['author']['displayName'];
					$author_nick = $i['author']['name'];
					$logged_time = $i['timeSpent'];
					$comment = $i['comment'];

				if (array_key_exists($author_nick, $team_members) && ((strtotime($updateDate)-strtotime($startDate))>172800) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) < strtotime($FilterEndDate))   {
						$issues_count_pilgrim += 1;
						echo "<tr>";
							echo "<td>";
							echo $key;
							echo "</td>";
							echo "<td>";
							echo $author_full;
							echo '</td>';
							echo "<td>";
							echo (strtotime($updateDate)-strtotime($startDate))/3600/24;
							echo ' days';
							echo '</td>';
							echo "<td>";
							echo $startDate;
							echo '</td>';
							echo "<td>";
							echo $updateDate;
							echo '</td>';
							echo "<td>";
							echo '<a data-toggle="tooltip" title="' . $comment . '">' . $logged_time . '</a>';
							echo '</td>';
						echo "</tr>";
					}
				}
			}

				echo "</tbody>";
				echo "</table>";
				echo '</div>';


// ************   LUMIERE STATS

		echo '<div id="lumiere" class="tab-pane fade">';
		$url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request_lumiere) . '&maxResults=1000';
		$team_members = $team_members_lumiere;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

		echo '<div class="well">Lumiere Pro</div>';
		echo '<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">';
		echo '<thead><tr>';
			echo '<th>Issue</th>';
			echo '<th>Author</th>';
			echo '<th>Difference</th>';
			echo '<th>Creation Time</th>';
			echo '<th>Update Time</th>';
			echo '<th>Logged Time</th>';
		echo '</thead></tr>';

		echo "<tbody>";

		$issues = json_decode(curl_exec($curl), true);
		foreach ($issues['issues'] as $issue) {
			$key = $issue['key'];
			curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key/worklog");
			$worklog = json_decode(curl_exec($curl), true);
			foreach ($worklog['worklogs'] as $i) {
				$startDate = substr($i['started'], 0, 10);
				$updateDate = substr($i['updated'], 0, 10);
				$author_full = $i['author']['displayName'];
				$author_nick = $i['author']['name'];
				$logged_time = $i['timeSpent'];
				$comment = $i['comment'];
				if (array_key_exists($author_nick, $team_members) && ((strtotime($updateDate)-strtotime($startDate))>172800) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) < strtotime($FilterEndDate))   {
					$issues_count_lumiere += 1;
					echo "<tr>";
						echo "<td>";
						echo $key;
						echo "</td>";
						echo "<td>";
						echo $author_full;
						echo '</td>';
						echo "<td>";
						echo (strtotime($updateDate)-strtotime($startDate))/3600/24;
						echo ' days';
						echo '</td>';
						echo "<td>";
						echo $startDate;
						echo '</td>';
						echo "<td>";
						echo $updateDate;
						echo '</td>';
						echo "<td>";
						echo '<a data-toggle="tooltip" title="' . $comment . '">' . $logged_time . '</a>';
						echo '</td>';
					echo "</tr>";
				}
			}
		}
			echo "</tbody>";
			echo "</table>";
			echo '</div>';

// ************   BLUEBERRY STATS

		echo '<div id="blueberry" class="tab-pane fade">';
		$url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request_blueberry) . '&maxResults=1000';
		$team_members = $team_members_blueberry;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

		echo '<div class="well">Lumiere Pro</div>';
		echo '<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">';
		echo '<thead><tr>';
			echo '<th>Issue</th>';
			echo '<th>Author</th>';
			echo '<th>Difference</th>';
			echo '<th>Creation Time</th>';
			echo '<th>Update Time</th>';
			echo '<th>Logged Time</th>';
		echo '</thead></tr>';

		echo "<tbody>";

		$issues = json_decode(curl_exec($curl), true);
		foreach ($issues['issues'] as $issue) {
			$key = $issue['key'];
			curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key/worklog");
			$worklog = json_decode(curl_exec($curl), true);
			foreach ($worklog['worklogs'] as $i) {
				$startDate = substr($i['started'], 0, 10);
				$updateDate = substr($i['updated'], 0, 10);
				$author_full = $i['author']['displayName'];
				$author_nick = $i['author']['name'];
				$logged_time = $i['timeSpent'];
				$comment = $i['comment'];
				if (array_key_exists($author_nick, $team_members) && ((strtotime($updateDate)-strtotime($startDate))>172800) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) < strtotime($FilterEndDate))   {
					$issues_count_blueberry += 1;
					echo "<tr>";
						echo "<td>";
						echo $key;
						echo "</td>";
						echo "<td>";
						echo $author_full;
						echo '</td>';
						echo "<td>";
						echo (strtotime($updateDate)-strtotime($startDate))/3600/24;
						echo ' days';
						echo '</td>';
						echo "<td>";
						echo $startDate;
						echo '</td>';
						echo "<td>";
						echo $updateDate;
						echo '</td>';
						echo "<td>";
						echo '<a data-toggle="tooltip" title="' . $comment . '">' . $logged_time . '</a>';
						echo '</td>';
					echo "</tr>";
				}
			}
		}
			echo "</tbody>";
			echo "</table>";
			echo '</div>';





	echo '</div>';
	?>
	</div>

<script type="text/javascript">
issues_count_scopic = '<?php echo $issues_count_scopic ;?>';
issues_count_lumiere = '<?php echo $issues_count_lumiere ;?>';
issues_count_pilgrim = '<?php echo $issues_count_pilgrim ;?>';
issues_count_blueberry = '<?php echo $issues_count_blueberry ;?>';

$(document).ready(function() {
		document.getElementById("badge_scopic").innerHTML = issues_count_scopic;
		document.getElementById("badge_lumiere").innerHTML = issues_count_lumiere;
		document.getElementById("badge_pilgrim").innerHTML = issues_count_pilgrim;
		document.getElementById("badge_blueberry").innerHTML = issues_count_blueberry;
});

$(document).ready(function(){
		$('.collapse').collapse({
			toggle: false
		})
		$('[data-toggle="tooltip"]').tooltip({html: true});
});

</script>

</body>
</html>
