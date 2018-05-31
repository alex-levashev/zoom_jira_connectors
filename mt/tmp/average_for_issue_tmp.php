<html>
<head>
		<title>Average hours per complexity</title>
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
		if($_GET['jira_filter'] != '') {
				$request = $_GET['jira_filter'];
		} else {
				$request = '"Discovered During" = customer AND status = Closed AND "Resolved By" in (pilgrim.ssover, pilgrim.aambartsumya, ext.gbolyuba, lumiere.vli, lumiere.ppribyl, lumiere.dnguyen, lumiere.akapelonis, lumiere.mblaha)';
				// $request = 'status = Closed';
		}
	?>
	<center><h2>Average time stats per filter</h2></center>
	<form class="form-horizontal" action="average_for_issue_tmp.php">
	    <div class="form-group">
	      <label class="control-label col-sm-1" for="jira_filter">Jira Filter:</label>
	      <div class="col-sm-10">
					<?php
					echo '<input class="form-control" id="jira_filter" placeholder="' . $request . '" name="jira_filter">';
					?>
	      </div>
	    </div>
	    <div class="form-group">
	      <div class="col-sm-offset-1 col-sm-10">
	        <button type="submit" class="btn btn-default">Submit</button>
	      </div>
	    </div>
	  </form>

	<?php

	if (file_exists('../int/teams_rates.php')) {
		require_once("../int/teams_rates.php");
	}

	$stats_s = 0;
	$stats_m = 0;
	$stats_l = 0;
	$stats_xl = 0;
	$stats_xxl = 0;
	$stats_no  = 0;
	$qty_s = 0;
	$qty_m = 0;
	$qty_l = 0;
	$qty_xl = 0;
	$qty_xxl = 0;
	$qty_no = 0;


	if (file_exists('../service/config/config.php')) {
		require_once("../service/config/config.php");
	} else {
		require_once("../config/config.php");
	}

	echo '<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">';
		echo '<thead><tr>';
			echo '<th>Month</th>';
			echo '<th>S (Time)</th>';
			echo '<th>S (Q-ty)</th>';
			echo '<th>M (Time)</th>';
			echo '<th>M (Q-ty)</th>';
			echo '<th>L (Time)</th>';
			echo '<th>L (Q-ty)</th>';
			echo '<th>XL (Time)</th>';
			echo '<th>XL (Q-ty)</th>';
			echo '<th>XXL (Time)</th>';
			echo '<th>XXL (Q-ty)</th>';
			echo '<th>N/A (Time)</th>';
			echo '<th>N/A (Q-ty)</th>';
			echo '<th>Int Dev</th>';
			echo '<th>Ext Dev</th>';
			echo '<th>Int QA</th>';
			echo '<th>Ext QA</th>';
			echo '<th>DevOps</th>';
			echo '<th>Docs</th>';
		echo '</thead></tr>';
		echo "<tbody>";
			echo '<tr>';


	for ($ii = -12; $ii <= 0; $ii++) {

		$time_spent = 0;
		$int_devs_total = 0;
		$ext_devs_total = 0;
		$int_qa_total = 0;
		$ext_qa_total = 0;
		$int_devops_total = 0;
		$int_docs_total = 0;
		$stats_s = 0;
		$qty_s = 0;
		$stats_m = 0;
		$qty_m = 0;
		$stats_l = 0;
		$qty_l = 0;
		$stats_xl = 0;
		$qty_xl = 0;
		$stats_xxl = 0;
		$qty_xxl = 0;
		$stats_no = 0;
		$qty_nov = 0;
		$time_spent_sum = 0;
		$time_spent_total_sum = 0;
		$int_devs_total_sum = 0;
		$ext_devs_total_sum = 0;
		$int_qa_total_sum = 0;
		$ext_qa_total_sum = 0;
		$int_devops_total_sum = 0;
		$int_docs_total_sum = 0;

	$url_add = ' AND status changed to Closed DURING (startOfMonth(' . $ii . '),endOfMonth(' . $ii . '))';

	$url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request) . urlencode($url_add) . '&maxResults=1000';

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

	$issues = json_decode(curl_exec($curl), true);

	$count = 0;

	$all_authors = array();
	foreach ($issues['issues'] as $issue) {

		$key = $issue['key'];

		$fixedby = $issue['fields']['customfield_10085']['name'];
		$complexity = $issue['fields']['customfield_11506']['value'];
		$changelog = $issue['changelog']['histories'];

		curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key/worklog");
		$worklog = json_decode(curl_exec($curl), true);

		$time_spent = 0;
		$time_spent_total = 0;
		$int_devs_total = 0;
		$ext_devs_total = 0;
		$int_qa_total = 0;
		$ext_qa_total = 0;
		$int_devops_total = 0;
		$int_docs_total = 0;


		foreach ($worklog['worklogs'] as $i) {
			$startDate = substr($i['started'], 0, 10);
			$author_full = $i['author']['displayName'];
			$author_nick = $i['author']['name'];
			$logged_time = $i['timeSpentSeconds'];
			$time_spent_total += $logged_time/3600;
			array_push($all_authors, $author_nick);

			if ($author_nick == $fixedby) {
				$time_spent += $logged_time/3600;
			}
			if ($zoom_team[$author_nick]['type'] == 'Developer' & $zoom_team[$author_nick]['location'] == 'In') {
				$int_devs_total += $logged_time/3600;
			} elseif ($zoom_team[$author_nick]['type'] == 'Developer' & $zoom_team[$author_nick]['location'] == 'Out') {
				$ext_devs_total += $logged_time/3600;
			} elseif ($zoom_team[$author_nick]['type'] == 'QA' & $zoom_team[$author_nick]['location'] == 'In') {
				$int_qa_total += $logged_time/3600;
			} elseif ($zoom_team[$author_nick]['type'] == 'QA' & $zoom_team[$author_nick]['location'] == 'Out') {
				$ext_qa_total += $logged_time/3600;
			} elseif ($zoom_team[$author_nick]['type'] == 'DevOps') {
				$int_devops_total += $logged_time/3600;
			} elseif ($zoom_team[$author_nick]['type'] == 'Documentarist') {
				$int_docs_total += $logged_time/3600;
			} elseif ($zoom_team[$author_nick]['type'] == 'Support') {
				// NULL
			} else {
				echo $author_nick . '<br>';
			}
		}

				if($complexity == "S") {
					$stats_s += $time_spent_total/8;
					$qty_s += 1;
				}
				if ($complexity == "M") {
					$stats_m += $time_spent_total/8;
					$qty_m += 1;
				}
				if ($complexity == "L") {
					$stats_l += $time_spent_total/8;
					$qty_l += 1;
				}
				if ($complexity == "XL") {
					$stats_xl += $time_spent_total/8;
					$qty_xl += 1;
				}
				if ($complexity == "XXL") {
					$stats_xxl += $time_spent_total/8;
					$qty_xxl += 1;
				}
				if ($complexity == "") {
					$stats_no += $time_spent_total/8;
					$qty_no += 1;
				}


				$time_spent_sum += $time_spent;
				$time_spent_total_sum += $time_spent_total;
				$int_devs_total_sum += $int_devs_total;
				$ext_devs_total_sum += $ext_devs_total;
				$int_qa_total_sum += $int_qa_total;
				$ext_qa_total_sum += $ext_qa_total;
				$int_devops_total_sum += $int_devops_total;
				$int_docs_total_sum += $int_docs_total;
				$dev_percents = round(($int_devs_total + $ext_devs_total)/($int_devs_total + $ext_devs_total + $ext_qa_total + $int_qa_total), 2);
				$qa_percents = round(($ext_qa_total + $int_qa_total)/($int_devs_total + $ext_devs_total + $ext_qa_total + $int_qa_total), 2);

		}


					echo '<td>';
						echo $ii;
					echo '</td>';

					echo '<td>';
						echo round(($stats_s/$qty_s), 2);
					echo '</td>';

					echo '<td>';
						echo $qty_s;
					echo '</td>';

					echo '<td>';
						echo round(($stats_m/$qty_m), 2);
					echo '</td>';

					echo '<td>';
						echo $qty_m;
					echo '</td>';

					echo '<td>';
						echo round(($stats_l/$qty_l), 2);
					echo '</td>';

					echo '<td>';
						echo $qty_l;
					echo '</td>';

					echo '<td>';
						echo round(($stats_xl/$qty_xl), 2);
					echo '</td>';

					echo '<td>';
						echo $qty_xl;
					echo '</td>';

					echo '<td>';
						echo round(($stats_xxl/$qty_xxl), 2);
					echo '</td>';

					echo '<td>';
						echo $qty_xxl;
					echo '</td>';

					echo '<td>';
						echo round(($stats_no/$qty_no), 2);
					echo '</td>';

					echo '<td>';
						echo $qty_no;
					echo '</td>';

					echo '<td>';
						echo round(($int_devs_total_sum/8), 2);
					echo '</td>';

					echo '<td>';
						echo round(($ext_devs_total_sum/8), 2);
					echo '</td>';

					echo '<td>';
						echo round(($int_qa_total_sum/8), 2);
					echo '</td>';

					echo '<td>';
						echo round(($ext_qa_total_sum/8), 2);
					echo '</td>';

					echo '<td>';
						echo round(($int_devs_total_sum/8), 2);
					echo '</td>';

					echo '<td>';
						echo round(($int_docs_total_sum/8), 2);
					echo '</td>';
				echo '</tr>';



	}

				echo "</tbody>";
			echo "</table>";

	?>
	<script>
		var table = $('#example').DataTable( {
				"fixedHeader": {
						header: true,
						footer: true
				},
				"pageLength": 100,
		} );
		// $(".dataTables_wrapper").css("width","90%");
	</script>
</body>
</html>
