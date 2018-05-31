<html>
<head>
		<title>Invoices - ONLY CUSTOMER ISSUES</title>
		<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
		<link rel="shortcut icon" href="../favicon.gif" type="image/gif">
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
	<div>
	<?php

	if (file_exists('../procedures/proc.php')) {
    require_once("../procedures/proc.php");
  } else {
    echo 'No file with procedures!';
  }
	echo '<br>';
	DateTimePick();

	if (file_exists('../int/teams_rates.php')) {
		require_once("../int/teams_rates.php");
	}

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

		$request_lumiere = 'worklogAuthor in (' . implode(",", array_keys($team_members_lumiere)) . ') AND worklogDate >= ' . $FilterStartDate . ' AND worklogDate <= ' . $FilterEndDate . ' AND "Discovered During" = customer';
		$lumiere_total_hours = 0;
		$lumiere_total_cost = 0;

		$request_scopic = 'worklogAuthor in (' . implode(",", array_keys($team_members_scopic)) . ') AND worklogDate >= ' . $FilterStartDate . ' AND worklogDate <= ' . $FilterEndDate . ' AND "Discovered During" = customer';
    $scopic_total_hours = 0;
    $scopic_total_cost = 0;

		$request_pilgrim = 'worklogAuthor in (' . implode(",", array_keys($team_members_pilgrim)) . ') AND worklogDate >= ' . $FilterStartDate . ' AND worklogDate <= ' . $FilterEndDate . ' AND "Discovered During" = customer';
    $pilgrim_total_hours = 0;
    $pilgrim_total_cost = 0;

		$request_blueberry = 'worklogAuthor in (' . implode(",", array_keys($team_members_blueberry)) . ') AND worklogDate >= ' . $FilterStartDate . ' AND worklogDate <= ' . $FilterEndDate . ' AND "Discovered During" = customer';
    $blueberry_total_hours = 0;
    $blueberry_total_cost = 0;


// ************   SCOPIC STATS
		echo '<div id="scopic" class="tab-pane fade in active">';
		$url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request_scopic) . '&maxResults=1000';
		$url2 =  'http://jira:81/issues/?jql=' . urlencode($request_scopic) . '&maxResults=1000';
		$team_members = $team_members_scopic;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);



		echo '<div class="well"><a href="' . $url2 . '" target="_blank">Scopic Software</a></div>';
		echo '<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">';
		echo '<thead><tr>';
			echo '<th>Name</th>';
			echo '<th>Rate</th>';
			echo '<th>Hours</th>';
			echo '<th>Cost</th>';
		echo '</thead></tr>';

		echo "<tbody>";


		$issues = json_decode(curl_exec($curl), true);

		echo "<center><button type='button' class='btn btn-default' data-toggle='collapse' data-target='#issues_button_scopic'>Issues need your attention</button></center>";
		echo "<div id='issues_button_scopic' class='text-center collapse'>";

		foreach ($issues['issues'] as $issue) {
			$key = $issue['key'];

			$issue_close_date = strtotime($issue['fields']['resolutiondate']);
			$issue_status = $issue['fields']['status']['name'];
			$issue_complexity = $issue['fields']['customfield_11506']['value'];

			if( ($issue_status == 'Closed') & ($issue_complexity == '') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_no_scopics += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'S') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_s_scopics += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'M') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_m_scopics += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'L') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_l_scopics += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'XL') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_xl_scopics += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'XXL') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate))  ) {
				$closed_issues_xxl_scopics += 1;
			}

			$changelog = $issue['changelog']['histories'];
			foreach ($changelog as $i) {

				foreach ($i['items'] as $item) {
					if(in_array('timeoriginalestimate', $item) && $item['fromString']>0 && !empty($item['fromString'])) {
    				echo '<strong>' . date( "Y-m-d H:i", strtotime($i[created])) . '</strong>';
						echo ' Estimate changed from ';

						if(empty($item['fromString'])) {
							echo '0 to ';
						}
						else {
							echo $item['fromString']/3600 . ' hours to ';
						}

						if(empty($item['toString'])) {
							echo '0';
						}
						else {
							echo $item['toString']/3600 . ' hours';
						}
						echo ' in ';
						echo $key;
						echo ' by ';
						echo $i[author][displayName];
						echo '<br>';
						// print_r($i);
						// echo '</div>';
					}
				}
			}

			curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key/worklog");
			$worklog = json_decode(curl_exec($curl), true);
			curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key?fields=timetracking");
			$timetracking = json_decode(curl_exec($curl), true);
			$original_estimate = $timetracking['fields']['timetracking']['originalEstimateSeconds']/3600;
			$time_spent = 0;
			$time_spent_total = 0;
			foreach ($worklog['worklogs'] as $i) {
        $startDate = substr($i['started'], 0, 10);
				$author_full = $i['author']['displayName'];
				$author_nick = $i['author']['name'];
				$logged_time = $i['timeSpentSeconds'];
        if (array_key_exists($author_nick, $team_members) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) <= strtotime($FilterEndDate)) {
          $team_members[$author_nick]['hours'] += $logged_time/3600;
          // echo $key . ' | ' .  $author_nick . ' | ' .  $team_members[$author_nick]['hours'] . ' | ' .  $startDate . '<br>';
          $team_members[$author_nick]['cost'] += ($logged_time/3600)*$team_members[$author_nick]['rate'];
					$team_members[$author_nick]['full_name'] = $author_full;
					$time_spent += $logged_time/3600;
				}
			}
			foreach ($worklog['worklogs'] as $i) {
				$author_full = $i['author']['displayName'];
				$author_nick = $i['author']['name'];
				$logged_time = $i['timeSpentSeconds'];
        if (array_key_exists($author_nick, $team_members)) {
					$time_spent_total += $logged_time/3600;
				}
			}
			if ($time_spent > $original_estimate) {
				echo '<div style="display:inline-block; width:100px" class="label label-success label-as-badge">' . $key . '</div>   ';
				echo ' Time spent     <span class="badge">' . sprintf("%07.2f", $time_spent_total) . '</span>  ';
				echo ' Time estimated <span class="badge">' . sprintf("%07.2f", $original_estimate) . '</span>  ';
				echo '<br>';
			}
		}
		echo '</div>';
		echo '<br>';
		echo '<br>';





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
        if (array_key_exists($author_nick, $team_members) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) <= strtotime($FilterEndDate)) {
          $team_members[$author_nick]['hours'] += $logged_time/3600;
          // echo $key . ' | ' .  $author_nick . ' | ' .  $team_members[$author_nick]['hours'] . ' | ' .  $startDate . '<br>';
          $team_members[$author_nick]['cost'] += ($logged_time/3600)*$team_members[$author_nick]['rate'];
					$team_members[$author_nick]['full_name'] = $author_full;
				}
			}
		}
    foreach ($team_members as $i  => $val) {
			if ($val['full_name'] != '') {
	      echo "<tr>";
	        echo "<td>";
	        echo $val['full_name'];
	        echo "</td>";
	        echo "<td>";
	        echo $val['rate'];
	        echo "</td>";
	        echo "<td>";
	        echo round($val['hours'], 2);
	        $scopic_total_hours += $val['hours'];
	        echo "</td>";
	        echo "<td>";
	        echo round($val['cost'], 2);
	        $scopic_total_cost += $val['cost'];
	        echo "</td>";
	      echo "</tr>";
			}
    }

    echo "</tbody>";
		echo "</table>";
    echo '<center><h4><span class="label label-primary">Total time is : ' . round($scopic_total_hours, 2) . ' Hours</span></h4><br>';
    echo '<h4><span class="label label-primary">Total cost is : ' . round($scopic_total_cost, 2) . ' USD</span></h4><br>';
		echo '<h4><span class="label label-primary">FTE is : ' . round($scopic_total_hours/160, 2) . '</span></h4></center>';

		echo "<center><button type='button' class='btn btn-default' data-toggle='collapse' data-target='#issues_complexity_scopic'>Issues Complexity Info</button></center>";
		echo "<div id='issues_complexity_scopic' class='text-center collapse'>";

		echo '<center><h4><table class="table table-bordered" style="width:50%"><tr><td>Closed issues with no complexity</td><td>' . $closed_issues_no_scopics . '</td></tr>';
		echo '<h4><tr><td>Closed issues with S complexity</td><td>' . $closed_issues_s_scopics . '</td></tr>';
		echo '<h4><tr><td>Closed issues with M complexity</td><td>' . $closed_issues_m_scopics . '</td></tr>';
		echo '<h4><tr><td>Closed issues with L complexity</td><td>' . $closed_issues_l_scopics . '</td></tr>';
		echo '<h4><tr><td>Closed issues with XL complexity</td><td>' . $closed_issues_xl_scopics . '</td></tr>';
		echo '<h4><tr><td>Closed issues with XXL complexity</td><td>' . $closed_issues_xxl_scopics . '</td></tr>';
		$closed_issues_scopics_hours = ($closed_issues_s_scopics * 2 + $closed_issues_m_scopics * 7 + $closed_issues_l_scopics * 14 + $closed_issues_xl_scopics * 30 + $closed_issues_xxl_scopics * 40) * 8;
		echo '<h4><tr><td>Total hours estimated</td><td>' . $closed_issues_scopics_hours . '</td></tr>';
		echo '</table></center>';

		echo '</div>';

		echo '</div>';

// ************   PILGRIM STATS
			echo '<div id="pilgrim" class="tab-pane fade">';
      $url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request_pilgrim) . '&maxResults=1000';
			$url2 =  'http://jira:81/issues/?jql=' . urlencode($request_pilgrim) . '&maxResults=1000';
  		$team_members = $team_members_pilgrim;

  		$curl = curl_init();
  		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
  		curl_setopt($curl, CURLOPT_URL, $url);
  		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
  		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);



  		echo '<div class="well"><a href="' . $url2 . '" target="_blank">Pilgrim</a></div>';
  		echo '<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">';
  		echo '<thead><tr>';
  			echo '<th>Name</th>';
  			echo '<th>Rate</th>';
  			echo '<th>Hours</th>';
  			echo '<th>Cost</th>';
  		echo '</thead></tr>';

  		echo "<tbody>";


			$issues = json_decode(curl_exec($curl), true);

			echo "<center><button type='button' class='btn btn-default' data-toggle='collapse' data-target='#issues_button_pilgrim'>Issues need your attention</button></center>";
			echo "<div id='issues_button_pilgrim' class='text-center collapse'>";

			foreach ($issues['issues'] as $issue) {
	      $key = $issue['key'];

				$issue_close_date = strtotime($issue['fields']['resolutiondate']);
				$issue_status = $issue['fields']['status']['name'];
				$issue_complexity = $issue['fields']['customfield_11506']['value'];

				if( ($issue_status == 'Closed') & ($issue_complexity == '') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
					$closed_issues_no_pilgrim += 1;
				}
				elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'S') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
					$closed_issues_s_pilgrim += 1;
				}
				elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'M') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
					$closed_issues_m_pilgrim += 1;
				}
				elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'L') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
					$closed_issues_l_pilgrim += 1;
				}
				elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'XL') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
					$closed_issues_xl_pilgrim += 1;
				}
				elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'XXL') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate))  ) {
					$closed_issues_xxl_pilgrim += 1;
				}



				$changelog = $issue['changelog']['histories'];
				foreach ($changelog as $i) {

					foreach ($i['items'] as $item) {
						if(in_array('timeoriginalestimate', $item) && $item['fromString']>0 && !empty($item['fromString'])) {
							echo '<div style="display:inline-block; width:100px" class="label label-success label-as-badge">' . date( "Y-m-d H:i", strtotime($i[created])) . '</div>   ';
							echo ' Estimate changed from ';

							if(empty($item['fromString'])) {
								echo '<span class="badge">0000.00 hours</span> to ';
							}
							else {
								echo '<span class="badge">' . sprintf("%07.2f", $item['fromString']/3600) . '</span> hours to ';
							}

							if(empty($item['toString'])) {
								echo '<span class="badge">0000.00</span>  hours';
							}
							else {
								echo '<span class="badge">' . sprintf("%07.2f", $item['toString']/3600) . '</span> hours';
							}
							echo ' in ';
							echo '<div style="display:inline-block; width:100px" class="label label-success label-as-badge">' . $key . '</div>   ';
							echo ' by ';
							echo '<div style="display:inline-block; width:200px" class="label label-success label-as-badge">' . $i[author][displayName] . '</div>   ';
							echo '<br>';
							// print_r($i);
							// echo '</div>';
						}
					}
				}

				curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key/worklog");
				$worklog = json_decode(curl_exec($curl), true);
				curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key?fields=timetracking");
				$timetracking = json_decode(curl_exec($curl), true);
				$original_estimate = $timetracking['fields']['timetracking']['originalEstimateSeconds']/3600;
				$time_spent = 0;
				$time_spent_total = 0;
				foreach ($worklog['worklogs'] as $i) {
	        $startDate = substr($i['started'], 0, 10);
					$author_full = $i['author']['displayName'];
					$author_nick = $i['author']['name'];
					$logged_time = $i['timeSpentSeconds'];
	        if (array_key_exists($author_nick, $team_members) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) <= strtotime($FilterEndDate)) {
	          $team_members[$author_nick]['hours'] += $logged_time/3600;
	          // echo $key . ' | ' .  $author_nick . ' | ' .  $team_members[$author_nick]['hours'] . ' | ' .  $startDate . '<br>';
	          $team_members[$author_nick]['cost'] += ($logged_time/3600)*$team_members[$author_nick]['rate'];
						$team_members[$author_nick]['full_name'] = $author_full;
						$time_spent += $logged_time/3600;
					}
				}
				foreach ($worklog['worklogs'] as $i) {
					$author_full = $i['author']['displayName'];
					$author_nick = $i['author']['name'];
					$logged_time = $i['timeSpentSeconds'];
	        if (array_key_exists($author_nick, $team_members)) {
						$time_spent_total += $logged_time/3600;
					}
				}
				if ($time_spent > $original_estimate) {
					echo '<div style="display:inline-block; width:100px" class="label label-success label-as-badge">' . $key . '</div>   ';
					echo ' Time spent     <span class="badge">' . sprintf("%07.2f", $time_spent_total) . '</span>  ';
					echo ' Time estimated <span class="badge">' . sprintf("%07.2f", $original_estimate) . '</span>  ';
					echo '<br>';
				}
			}
			echo '</div>';
			echo '<br>';
			echo '<br>';







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
          if (array_key_exists($author_nick, $team_members) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) <= strtotime($FilterEndDate)) {
            $team_members[$author_nick]['hours'] += $logged_time/3600;
            // echo $key . ' | ' .  $author_nick . ' | ' .  $team_members[$author_nick]['hours'] . ' | ' .  $startDate . '<br>';
            $team_members[$author_nick]['cost'] += ($logged_time/3600)*$team_members[$author_nick]['rate'];
						$team_members[$author_nick]['full_name'] = $author_full;
  				}
  			}
  		}
      foreach ($team_members as $i  => $val) {
					if ($val['full_name'] != '') {
	        echo "<tr>";
	          echo "<td>";
	          echo $val['full_name'];
	          echo "</td>";
	          echo "<td>";
	          echo $val['rate'];
	          echo "</td>";
	          echo "<td>";
	          echo round($val['hours'], 2);
	          $pilgrim_total_hours += $val['hours'];
	          echo "</td>";
	          echo "<td>";
	          echo round($val['cost'], 2);
	          $pilgrim_total_cost += $val['cost'];
	          echo "</td>";
	        echo "</tr>";
				}
      }

      echo "</tbody>";
  		echo "</table>";
      echo '<center><h4><span class="label label-primary">Total time is : ' . round($pilgrim_total_hours, 2) . ' Hours</span></h4><br>';
      echo '<h4><span class="label label-primary">Total cost is : ' . round($pilgrim_total_cost, 2) . ' USD</span></h4><br>';
			echo '<h4><span class="label label-primary">FTE is : ' . round($pilgrim_total_hours/160, 2) . '</span></h4></center>';

			echo "<center><button type='button' class='btn btn-default' data-toggle='collapse' data-target='#issues_complexity_pilgrim'>Issues Complexity Info</button></center>";
			echo "<div id='issues_complexity_pilgrim' class='text-center collapse'>";

			echo '<center><h4><table class="table table-bordered" style="width:50%"><tr><td>Closed issues with no complexity</td><td>' . $closed_issues_no_pilgrim . '</td></tr>';
			echo '<h4><tr><td>Closed issues with S complexity</td><td>' . $closed_issues_s_pilgrim . '</td></tr>';
			echo '<h4><tr><td>Closed issues with M complexity</td><td>' . $closed_issues_m_pilgrim . '</td></tr>';
			echo '<h4><tr><td>Closed issues with L complexity</td><td>' . $closed_issues_l_pilgrim . '</td></tr>';
			echo '<h4><tr><td>Closed issues with XL complexity</td><td>' . $closed_issues_xl_pilgrim . '</td></tr>';
			echo '<h4><tr><td>Closed issues with XXL complexity</td><td>' . $closed_issues_xxl_pilgrim . '</td></tr>';
			$closed_issues_pilgrim_hours = ($closed_issues_s_pilgrim * 2 + $closed_issues_m_pilgrim * 7 + $closed_issues_l_pilgrim * 14 + $closed_issues_xl_pilgrim * 30 + $closed_issues_xxl_pilgrim * 40) * 8;
			echo '<h4><tr><td>Total hours estimated</td><td>' . $closed_issues_pilgrim_hours . '</td></tr>';
			echo '</table></center>';

			echo '</div>';

			echo '</div>';


// ************   LUMIERE STATS

		echo '<div id="lumiere" class="tab-pane fade">';
    $url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request_lumiere) . '&maxResults=1000&expand=changelog';
		$url2 =  'http://jira:81/issues/?jql=' . urlencode($request_lumiere) . '&maxResults=1000';
		$team_members = $team_members_lumiere;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);



		echo '<div class="well"><a href="' . $url2 . '" target="_blank">Lumiere Pro</a></div>';
		echo '<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">';
		echo '<thead><tr>';
			echo '<th>Name</th>';
			echo '<th>Rate</th>';
			echo '<th>Hours</th>';
			echo '<th>Cost</th>';
		echo '</thead></tr>';

		echo "<tbody>";



		$issues = json_decode(curl_exec($curl), true);

		echo "<center><button type='button' class='btn btn-default' data-toggle='collapse' data-target='#issues_button_lumiere'>Issues need your attention</button></center>";
		echo "<div id='issues_button_lumiere' class='text-center collapse'>";

		foreach ($issues['issues'] as $issue) {
      $key = $issue['key'];

			$issue_close_date = strtotime($issue['fields']['resolutiondate']);
			$issue_status = $issue['fields']['status']['name'];
			$issue_complexity = $issue['fields']['customfield_11506']['value'];

			if( ($issue_status == 'Closed') & ($issue_complexity == '') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_no_lumiere += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'S') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_s_lumiere += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'M') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_m_lumiere += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'L') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_l_lumiere += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'XL') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_xl_lumiere += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'XXL') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate))  ) {
				$closed_issues_xxl_lumiere += 1;
			}

			$changelog = $issue['changelog']['histories'];
			foreach ($changelog as $i) {

				foreach ($i['items'] as $item) {
					if(in_array('timeoriginalestimate', $item) && $item['fromString']>0 && !empty($item['fromString'])) {
						echo '<div style="display:inline-block; width:100px" class="label label-success label-as-badge">' . date( "Y-m-d H:i", strtotime($i[created])) . '</div>   ';
						echo ' Estimate changed from ';

						if(empty($item['fromString'])) {
							echo '<span class="badge">0000.00 hours</span> to ';
						}
						else {
							echo '<span class="badge">' . sprintf("%07.2f", $item['fromString']/3600) . '</span> hours to ';
						}

						if(empty($item['toString'])) {
							echo '<span class="badge">0000.00</span>  hours';
						}
						else {
							echo '<span class="badge">' . sprintf("%07.2f", $item['toString']/3600) . '</span> hours';
						}
						echo ' in ';
						echo '<div style="display:inline-block; width:100px" class="label label-success label-as-badge">' . $key . '</div>   ';
						echo ' by ';
						echo '<div style="display:inline-block; width:200px" class="label label-success label-as-badge">' . $i[author][displayName] . '</div>   ';
						echo '<br>';
						// print_r($i);
						// echo '</div>';
					}
				}
			}

			curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key/worklog");
			$worklog = json_decode(curl_exec($curl), true);
			curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key?fields=timetracking");
			$timetracking = json_decode(curl_exec($curl), true);
			$original_estimate = $timetracking['fields']['timetracking']['originalEstimateSeconds']/3600;
			$time_spent = 0;
			$time_spent_total = 0;
			foreach ($worklog['worklogs'] as $i) {
        $startDate = substr($i['started'], 0, 10);
				$author_full = $i['author']['displayName'];
				$author_nick = $i['author']['name'];
				$logged_time = $i['timeSpentSeconds'];
        if (array_key_exists($author_nick, $team_members) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) <= strtotime($FilterEndDate)) {
          $team_members[$author_nick]['hours'] += $logged_time/3600;
          // echo $key . ' | ' .  $author_nick . ' | ' .  $team_members[$author_nick]['hours'] . ' | ' .  $startDate . '<br>';
          $team_members[$author_nick]['cost'] += ($logged_time/3600)*$team_members[$author_nick]['rate'];
					$team_members[$author_nick]['full_name'] = $author_full;
					$time_spent += $logged_time/3600;
// VALERIY'S MANAGEMENT HOUTS ITM-448
								if ($author_nick == 'lumiere.vli' && $key == 'ITM-448') {
									$valery_management_hours += $logged_time/3600;
									$valery_management_cost += ($logged_time/3600)*400;
								}
// END OF VALERIY'S MANAGEMENT HOURS ITM-448
				}
			}

			foreach ($worklog['worklogs'] as $i) {
				$author_full = $i['author']['displayName'];
				$author_nick = $i['author']['name'];
				$logged_time = $i['timeSpentSeconds'];
        if (array_key_exists($author_nick, $team_members)) {
					$time_spent_total += $logged_time/3600;
				}
			}
			if ($time_spent > $original_estimate) {
				echo '<div style="display:inline-block; width:100px" class="label label-success label-as-badge">' . $key . '</div>   ';
				echo ' Time spent     <span class="badge">' . sprintf("%07.2f", $time_spent_total) . '</span>  ';
				echo ' Time estimated <span class="badge">' . sprintf("%07.2f", $original_estimate) . '</span>  ';
				echo '<br>';
			}
		}
		echo '</div>';
		echo '<br>';
		echo '<br>';




    foreach ($team_members as $i  => $val) {
			if ($val['full_name'] != '') {
				echo "<tr>";
	        echo "<td>";
	        echo $val['full_name'];
	        echo "</td>";
	        echo "<td>";
	        echo $val['rate'];
	        echo "</td>";
	        echo "<td>";
	        echo round($val['hours'], 2);
	        $lumiere_total_hours += $val['hours'];
	        echo "</td>";
	        echo "<td>";
	        echo round($val['cost'], 2);
	        $lumiere_total_cost += $val['cost'];
	        echo "</td>";
	      echo "</tr>";
			}
    }

    echo "</tbody>";
		echo "</table>";
    echo '<center><h4><span class="label label-primary">Total time is : ' . round($lumiere_total_hours, 2) . ' Hours + ' . round($valery_management_hours, 2) . ' management hours </span></h4><br>';
		echo '<h4><span class="label label-primary">Total cost is : ' . round($lumiere_total_cost, 2) . ' CZK + ' . round($valery_management_cost, 2) . ' CZK for management taks, total is : ' . round(($lumiere_total_cost + $valery_management_cost), 2) . ' CZK</span></h4><br>';
		echo '<h4><span class="label label-primary">Total cost is : ' . convertCurrency(round($lumiere_total_cost, 2), "CZK", "USD") . ' USD + ' . convertCurrency(round($valery_management_cost, 2), "CZK", "USD") . ' USD for management taks, total is : ' . convertCurrency((round(($lumiere_total_cost + $valery_management_cost), 2)), "CZK", "USD") . ' USD</span></h4><br>';
		echo '<h4><span class="label label-primary">FTE is : ' . round($lumiere_total_hours/160, 2) . '</span></h4></center>';

		echo "<center><button type='button' class='btn btn-default' data-toggle='collapse' data-target='#issues_complexity_lumiere'>Issues Complexity Info</button></center>";
		echo "<div id='issues_complexity_lumiere' class='text-center collapse'>";

		echo '<center><h4><table class="table table-bordered" style="width:50%"><tr><td>Closed issues with no complexity</td><td>' . $closed_issues_no_lumiere . '</td></tr>';
		echo '<h4><tr><td>Closed issues with S complexity</td><td>' . $closed_issues_s_lumiere . '</td></tr>';
		echo '<h4><tr><td>Closed issues with M complexity</td><td>' . $closed_issues_m_lumiere . '</td></tr>';
		echo '<h4><tr><td>Closed issues with L complexity</td><td>' . $closed_issues_l_lumiere . '</td></tr>';
		echo '<h4><tr><td>Closed issues with XL complexity</td><td>' . $closed_issues_xl_lumiere . '</td></tr>';
		echo '<h4><tr><td>Closed issues with XXL complexity</td><td>' . $closed_issues_xxl_lumiere . '</td></tr>';
		$closed_issues_lumiere_hours = ($closed_issues_s_lumiere * 2 + $closed_issues_m_lumiere * 7 + $closed_issues_l_lumiere * 14 + $closed_issues_xl_lumiere * 30 + $closed_issues_xxl_lumiere * 40) * 8;
		echo '<h4><tr><td>Total hours estimated</td><td>' . $closed_issues_lumiere_hours . '</td></tr>';
		echo '</table></center>';
		echo '</div>';

		echo '</div>';

// ************   BLUEBERRY STATS

		echo '<div id="blueberry" class="tab-pane fade">';
    $url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request_blueberry) . '&maxResults=1000';
		$url2 =  'http://jira:81/issues/?jql=' . urlencode($request_blueberry) . '&maxResults=1000';
		$team_members = $team_members_blueberry;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);



		echo '<div class="well"><a href="' . $url2 . '" target="_blank">Blueberry</a></div>';
		echo '<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">';
		echo '<thead><tr>';
			echo '<th>Name</th>';
			echo '<th>Rate</th>';
			echo '<th>Hours</th>';
			echo '<th>Cost</th>';
		echo '</thead></tr>';

		echo "<tbody>";

		$issues = json_decode(curl_exec($curl), true);

		echo "<center><button type='button' class='btn btn-default' data-toggle='collapse' data-target='#issues_button_blueberry'>Issues need your attention</button></center>";
		echo "<div id='issues_button_blueberry' class='text-center collapse'>";

		foreach ($issues['issues'] as $issue) {
      $key = $issue['key'];

			$issue_close_date = strtotime($issue['fields']['resolutiondate']);
			$issue_status = $issue['fields']['status']['name'];
			$issue_complexity = $issue['fields']['customfield_11506']['value'];

			if( ($issue_status == 'Closed') & ($issue_complexity == '') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_no_blueberry += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'S') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_s_blueberry += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'M') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_m_blueberry += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'L') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_l_blueberry += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'XL') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate)) ) {
				$closed_issues_xl_blueberry += 1;
			}
			elseif ( ($issue_status == 'Closed') & ($issue_complexity == 'XXL') & ($issue_close_date >= strtotime($FilterStartDate)) & ($issue_close_date <= strtotime($FilterEndDate))  ) {
				$closed_issues_xxl_blueberry += 1;
			}

			$changelog = $issue['changelog']['histories'];
			foreach ($changelog as $i) {

				foreach ($i['items'] as $item) {
					if(in_array('timeoriginalestimate', $item) && $item['fromString']>0 && !empty($item['fromString'])) {
						echo '<div style="display:inline-block; width:100px" class="label label-success label-as-badge">' . date( "Y-m-d H:i", strtotime($i[created])) . '</div>   ';
						echo ' Estimate changed from ';

						if(empty($item['fromString'])) {
							echo '<span class="badge">0000.00 hours</span> to ';
						}
						else {
							echo '<span class="badge">' . sprintf("%07.2f", $item['fromString']/3600) . '</span> hours to ';
						}

						if(empty($item['toString'])) {
							echo '<span class="badge">0000.00</span>  hours';
						}
						else {
							echo '<span class="badge">' . sprintf("%07.2f", $item['toString']/3600) . '</span> hours';
						}
						echo ' in ';
						echo '<div style="display:inline-block; width:100px" class="label label-success label-as-badge">' . $key . '</div>   ';
						echo ' by ';
						echo '<div style="display:inline-block; width:200px" class="label label-success label-as-badge">' . $i[author][displayName] . '</div>   ';
						echo '<br>';
						// print_r($i);
						// echo '</div>';
					}
				}
			}

			curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key/worklog");
			$worklog = json_decode(curl_exec($curl), true);
			curl_setopt($curl, CURLOPT_URL, "http://jira:81/rest/api/2/issue/$key?fields=timetracking");
			$timetracking = json_decode(curl_exec($curl), true);
			$original_estimate = $timetracking['fields']['timetracking']['originalEstimateSeconds']/3600;
			$time_spent = 0;
			$time_spent_total = 0;
			foreach ($worklog['worklogs'] as $i) {
        $startDate = substr($i['started'], 0, 10);
				$author_full = $i['author']['displayName'];
				$author_nick = $i['author']['name'];
				$logged_time = $i['timeSpentSeconds'];
        if (array_key_exists($author_nick, $team_members) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) <= strtotime($FilterEndDate)) {
          $team_members[$author_nick]['hours'] += $logged_time/3600;
          // echo $key . ' | ' .  $author_nick . ' | ' .  $team_members[$author_nick]['hours'] . ' | ' .  $startDate . '<br>';
          $team_members[$author_nick]['cost'] += ($logged_time/3600)*$team_members[$author_nick]['rate'];
					$team_members[$author_nick]['full_name'] = $author_full;
					$time_spent += $logged_time/3600;
				}
			}
			foreach ($worklog['worklogs'] as $i) {
				$author_full = $i['author']['displayName'];
				$author_nick = $i['author']['name'];
				$logged_time = $i['timeSpentSeconds'];
        if (array_key_exists($author_nick, $team_members)) {
					$time_spent_total += $logged_time/3600;
				}
			}
			if ($time_spent > $original_estimate) {
				echo '<div style="display:inline-block; width:100px" class="label label-success label-as-badge">' . $key . '</div>   ';
				echo ' Time spent     <span class="badge">' . sprintf("%07.2f", $time_spent_total) . '</span>  ';
				echo ' Time estimated <span class="badge">' . sprintf("%07.2f", $original_estimate) . '</span>  ';
				echo '<br>';
			}
		}
		echo '</div>';
		echo '<br>';
		echo '<br>';




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
        if (array_key_exists($author_nick, $team_members) && strtotime($startDate) >= strtotime($FilterStartDate) && strtotime($startDate) <= strtotime($FilterEndDate)) {
          $team_members[$author_nick]['hours'] += $logged_time/3600;
          // echo $key . ' | ' .  $author_nick . ' | ' .  $team_members[$author_nick]['hours'] . ' | ' .  $startDate . '<br>';
          $team_members[$author_nick]['cost'] += ($logged_time/3600)*$team_members[$author_nick]['rate'];
					$team_members[$author_nick]['full_name'] = $author_full;
				}
			}
		}
    foreach ($team_members as $i  => $val) {
			if ($val['full_name'] != '') {
	      echo "<tr>";
	        echo "<td>";
	        echo $val['full_name'];
	        echo "</td>";
	        echo "<td>";
	        echo $val['rate'];
	        echo "</td>";
	        echo "<td>";
	        echo round($val['hours'], 2);
	        $blueberry_total_hours += $val['hours'];
	        echo "</td>";
	        echo "<td>";
	        echo round($val['cost'], 2);
	        $blueberry_total_cost += $val['cost'];
	        echo "</td>";
	      echo "</tr>";
			}
    }

    echo "</tbody>";
		echo "</table>";
    echo '<center><h4><span class="label label-primary">Total time is : ' . round($blueberry_total_hours, 2) . ' Hours</span></h4><br>';
		echo '<h4><span class="label label-primary">Total cost is : ' . round($blueberry_total_cost, 2) . ' CZK</span></h4><br>';
		echo '<h4><span class="label label-primary">Total cost is : ' . convertCurrency(round($blueberry_total_cost, 2), "CZK", "USD") . ' USD</span></h4>';
		echo '<h4><span class="label label-primary">FTE is : ' . round($blueberry_total_hours/160, 2) . '</span></h4></center>';

		echo "<center><button type='button' class='btn btn-default' data-toggle='collapse' data-target='#issues_complexity_blueberry'>Issues Complexity Info</button></center>";
		echo "<div id='issues_complexity_blueberry' class='text-center collapse'>";

		echo '<center><h4><table class="table table-bordered" style="width:50%"><tr><td>Closed issues with no complexity</td><td>' . $closed_issues_no_blueberry . '</td></tr>';
		echo '<h4><tr><td>Closed issues with S complexity</td><td>' . $closed_issues_s_blueberry . '</td></tr>';
		echo '<h4><tr><td>Closed issues with M complexity</td><td>' . $closed_issues_m_blueberry . '</td></tr>';
		echo '<h4><tr><td>Closed issues with L complexity</td><td>' . $closed_issues_l_blueberry . '</td></tr>';
		echo '<h4><tr><td>Closed issues with XL complexity</td><td>' . $closed_issues_xl_blueberry . '</td></tr>';
		echo '<h4><tr><td>Closed issues with XXL complexity</td><td>' . $closed_issues_xxl_blueberry . '</td></tr>';
		$closed_issues_blueberry_hours = ($closed_issues_s_blueberry * 2 + $closed_issues_m_blueberry * 7 + $closed_issues_l_blueberry * 14 + $closed_issues_xl_blueberry * 30 + $closed_issues_xxl_blueberry * 40) * 8;
		echo '<h4><tr><td>Total hours estimated</td><td>' . $closed_issues_blueberry_hours . '</td></tr>';
		echo '</table></center>';

		echo '</div>';

		echo '</div>';

	echo '</div>';
	?>
	</div>
	<?php
		echo '<br><center><h4><span class="label label-success">Total FTE is : ' . round((($blueberry_total_hours+$scopic_total_hours+$lumiere_total_hours+$pilgrim_total_hours)/160),2) . '</span></h4></center>';
	?>

</body>
</html>
