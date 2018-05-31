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
				$request = 'issuekey=SCR-827';
		}
	?>
	<center><h2>Average time stats per filter</h2></center>
	<form class="form-horizontal" action="filter_time_split.php">
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

	$url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request) . '&maxResults=1000';

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

	$issues = json_decode(curl_exec($curl), true);

	echo '<div class="container">';
	echo '<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">';
	echo '<thead><tr>';
		echo '<th>#</th>';
		echo '<th>Jira issue</th>';
		echo '<th>Fixed by</th>';
		echo '<th>Complexity</th>';
		echo '<th>Hours spent by developer</th>';
		echo '<th>Hours spent total</th>';
		echo '<th>Int devs</th>';
		echo '<th>Ext devs</th>';
		echo '<th>Int QA</th>';
		echo '<th>Ext QA</th>';
		echo '<th>Int DevOPs</th>';
		echo '<th>Docs</th>';
		echo '<th>Dev vs QA';
	echo '</thead></tr>';
	echo "<tbody>";

	$count = 0;

	$all_authors = array();
	$not_in_the_list_authors = array();
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
		$all_members = $zoom_team;


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



		// if ( $complexity != '' ) {
			echo "<tr>";
				echo "<td>";
				$count = $count + 1;
				echo $count;
				echo "</td>";
				echo "<td>";
				echo $key;
				echo "</td>";
				echo "<td>";
				echo $fixedby;
				echo "</td>";
				echo "<td>";
				echo $complexity;
				echo "</td>";

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

				echo "<td>";
				echo round($time_spent/8, 2) . ' d';
				$time_spent_sum += $time_spent;
				echo "</td>";
				echo "<td>";
				echo round($time_spent_total/8, 2) . ' d';
				$time_spent_total_sum += $time_spent_total;
				echo "</td>";
				echo "<td>";
				echo round($int_devs_total/8, 2) . ' d';
				$int_devs_total_sum += $int_devs_total;
				echo "</td>";
				echo "<td>";
				echo round($ext_devs_total/8, 2) . ' d';
				$ext_devs_total_sum += $ext_devs_total;
				echo "</td>";
				echo "<td>";
				echo round($int_qa_total/8, 2) . ' d';
				$int_qa_total_sum += $int_qa_total;
				echo "</td>";
				echo "<td>";
				echo round($ext_qa_total/8, 2) . ' d';
				$ext_qa_total_sum += $ext_qa_total;
				echo "</td>";
				echo "<td>";
				echo round($int_devops_total/8, 2) . ' d';
				$int_devops_total_sum += $int_devops_total;
				echo "</td>";
				echo "<td>";
				echo round($int_docs_total/8, 2) . ' d';
				$int_docs_total_sum += $int_docs_total;
				echo "</td>";
				echo "<td>";
				$dev_percents = round(($int_devs_total + $ext_devs_total)/($int_devs_total + $ext_devs_total + $ext_qa_total + $int_qa_total), 2);
				$qa_percents = round(($ext_qa_total + $int_qa_total)/($int_devs_total + $ext_devs_total + $ext_qa_total + $int_qa_total), 2);
				if ($dev_percents >= $qa_percents) {
					echo '<div class="alert alert-success">';
					echo $dev_percents;
					echo ' / ';
					echo $qa_percents;
					echo '</div>';
				} else {
					echo '<div class="alert alert-danger">';
					echo $dev_percents;
					echo ' / ';
					echo $qa_percents;
					echo '</div>';
				}
				echo "</td>";
			echo "</tr>";
		}
		echo "</tbody>";
		echo "<tfoot>";
			echo "<tr>";
				echo '<td colspan="4">';
				echo "Summary";
				echo "</td>";
				echo "<td>";
				echo round($time_spent_sum/8, 2) . ' d';
				echo "</td>";
				echo "<td>";
				echo round($time_spent_total_sum/8, 2) . ' d';
				echo "</td>";
				echo "<td>";
				echo round($int_devs_total_sum/8, 2) . ' d';
				echo "</td>";
				echo "<td>";
				echo round($ext_devs_total_sum/8, 2) . ' d';
				echo "</td>";
				echo "<td>";
				echo round($int_qa_total_sum/8, 2) . ' d';
				echo "</td>";
				echo "<td>";
				echo round($ext_qa_total_sum/8, 2) . ' d';
				echo "</td>";
				echo "<td>";
				echo round($int_devops_total_sum/8, 2) . ' d';
				echo "</td>";
				echo "<td>";
				echo round($int_docs_total_sum/8, 2) . ' d';
				echo "</td>";
				echo "<td>";
				echo "</td>";
			echo "</tr>";
		echo "</tfoot>";

	echo "</table>";
	echo '</div>';
	echo '<br>';
	echo '<div class="well"><center>';
	echo 'Average time for S is ' . round(($stats_s/$qty_s), 2) . ' days with ' . $qty_s . ' issues';
	echo '<br>';
	echo 'Average time for M ' . round(($stats_m/$qty_m), 2) . ' days with ' . $qty_m . ' issues';
	echo '<br>';
	echo 'Average time for L ' . round(($stats_l/$qty_l), 2) . ' days with ' . $qty_l . ' issues';
	echo '<br>';
	echo 'Average time for XL ' . round(($stats_xl/$qty_xl), 2) . ' days  with ' . $qty_xl . ' issues';
	echo '<br>';
	echo 'Average time for XXL ' . round(($stats_xxl/$qty_xxl), 2) . ' days  with ' . $qty_xxl . ' issues';
	echo '<br>';
	echo 'Average time issue w/o complexity ' . round(($stats_no/$qty_no), 2) . ' days  with ' . $qty_no . ' issues';
	// echo print_r($not_in_the_list_authors);
	echo '<br></center></div>';

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
