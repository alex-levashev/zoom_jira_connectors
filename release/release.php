	<!DOCTYPE html>
<html lang="en">

<head>
	<title>Release Info Page</title>
	<!-- <meta http-equiv="refresh" content="30"> -->
	<link rel="shortcut icon" href="favicon.gif" type="image/gif">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
			<link href="css/bootstrap.css" rel="stylesheet">
			<link href="css/bootstrap.css" rel="stylesheet">
			<script src="js/moment.js"></script>
			<script src="js/Chart.bundle.js"></script>
			<script src="js/Chart.js"></script>
			<script src="js/tether.min.js"></script>
			<script src="js/jquery.min.js"></script>
			<script src="js/bootstrap.min.js"></script>
			<script src="js/bootstrap.js"></script>

</head>

<body>
	<div class="container col-sm-12">
	  <div class="page-header">
			<?php
			$tag = '6.3.0';
			$epics = ['CAL-17536', 'CAL-17486', 'CAL-17520', 'CAL-17558', 'CAL-17636', 'CAL-17649'];
			echo '<h3 class="text-center">' . $tag . ' Release Info Page</h3>';
			?>
		</div>
		<div class="well well-sm">
			<?php
			date_default_timezone_set('Europe/Prague');
			$date = date('d/m/Y h:i:s', time());
			echo 'Refresh date : ' . $date;
			?>
		</div>
	</div>
	<div>
		<script>
		function draw_graph_js() {
			var ctx = document.getElementById(gn);
			var myChart = new Chart(ctx, {
			  type: 'horizontalBar',
			  data: {
			    datasets: [{
			        label: 'Done',
			        data: [a],
			        backgroundColor: [
			          'green'
			        ]
						},

			      {
			        label: 'In Progress',
			        data: [b],
			        backgroundColor: [
			          'yellow'
			        ]
			      },

						{
						label: 'To Do',
						data: [c],
						backgroundColor: [
							'red'
						]
					}

			    ]
			  },
			  options: {
			    scales: {
			      yAxes: [{
			        stacked: true,
			        ticks: {
			          beginAtZero: true
			        }
			      }],
			      xAxes: [{
			        stacked: true,
			        ticks: {
			          beginAtZero: true,
			        }
			      }]

			    }
			  }
			});
		}

		function draw_burn_js() {
			var config = {
			  type: 'line',
			  data: {
			    labels: glabels,
			    datasets: [{
			      label: "Points",
			      data: gdata,
						borderColor: 'green',
						backgroundColor: 'white',
						fill: false,
			    },
				]
			  },
			  options: {
			    scales: {
			      xAxes: [{
			        type: 'time',
			        time: {
			          displayFormats: {
			          	'millisecond': 'YYYY-MM-DD',
			            'second': 'YYYY-MM-DD',
			            'minute': 'YYYY-MM-DD',
			            'hour': 'YYYY-MM-DD',
			            'day': 'YYYY-MM-DD',
			            'week': 'YYYY-MM-DD',
			            'month': 'YYYY-MM-DD',
			            'quarter': 'YYYY-MM-DD',
			            'year': 'YYYY-MM-DD',
			          }
			        }
			      }],
			    },
			  }
			};

			var ctx = document.getElementById(gburn).getContext("2d");
			new Chart(ctx, config);


		}

		</script>
		<?php

			if (file_exists('service/config/config.php')) {
				require_once("service/config/config.php");
			} else {
				require_once("config/config.php");
			}



			function issues_count($request_filter,$username,$password) {

				$request_url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request_filter) . '&maxResults=1000';
				// echo '<a href="http://jira:81/issues/?jql='. urlencode($request_filter) . '" target="_blank">LINK</a>';
				// echo '</br>';
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
				curl_setopt($curl, CURLOPT_URL, $request_url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
				$total_issues_count = json_decode(curl_exec($curl), true)['total'];
			 	return $total_issues_count;
			}

			function issues_count_hours($request_filter,$username,$password) {

				$url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request_filter) . '&maxResults=1000';

				$curl = curl_init();
				curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

				$issue_list = (curl_exec($curl));
				$issue_list_decoded=json_decode($issue_list);

				$t_shirt_sizes_total_value = 0;
				$t_shirt_sizes = array( "S"=>"2", "M"=>"7", "L"=>"14", "XL"=>"30","XXL"=>"70");
				// print_r($issue_list_decoded);
				foreach($issue_list_decoded->issues as $i) {
					$t_shirt_size_letter = $i->fields->customfield_11506->value;
					$t_shirt_size_value = $t_shirt_sizes[$t_shirt_size_letter];
					$t_shirt_sizes_total_value = $t_shirt_sizes_total_value + $t_shirt_size_value;
				}
				return $t_shirt_sizes_total_value;
			}

			function issue_title($issue_key,$username,$password) {
				$request_url = 'http://jira:81/rest/api/2/issue/' . $issue_key;
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
				curl_setopt($curl, CURLOPT_URL, $request_url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
				$issue_summary = json_decode(curl_exec($curl), true)['fields']['summary'];
				return $issue_summary;
			}

			function draw_graph($filter1, $filter2, $filter3, $username, $password, $graph_name, $graph_label, $height, $cols) {
				$count1 = issues_count($filter1, $username, $password);
				$count2 = issues_count($filter2, $username, $password);
				$count3 = issues_count($filter3, $username, $password);
				echo '<div class="container col-sm-' . $cols . '">';
				echo '<h5 class="text-center">' . $graph_label . '</h5>';
				echo '<canvas id="' . $graph_name . '" width="100%" height="' . $height . '%"></canvas>';
				echo '</div>';
				echo '<script>';
				echo 'var a = "' . $count1 . '";';
				echo 'var b = "' . $count2 . '";';
				echo 'var c = "' . $count3 . '";';
				echo 'var gn = "' . $graph_name . '";';
				echo 'draw_graph_js()';
				echo '</script>';
			}

			function draw_burn($file, $graph_name, $graph_label, $cols) {
				$date_array = "";
				$count_array = "";
				$csv = array_map('str_getcsv', file($file));
				unset($csv[0]);
				foreach ($csv as $key => $value) {
					if($date_array == '') {
						$date_array = '"' . $value[0] . '"';
					} else {
						$date_array = $date_array . ', "' . $value[0] . '"';
					}

					if($count_array == '') {
						$count_array = $value[1];
					} else {
						$count_array = $count_array . ', ' . $value[1];
					}

				}

				echo '<div class="container col-sm-' . $cols . '">';
				echo '<h5 class="text-center">' . $graph_label . '</h5>';
				echo '<canvas id="' . $graph_name . '" width="100%" height="40%"></canvas>';
				echo '<script>';
				echo 'var gburn = "' . $graph_name . '";';
				echo 'var glabels = [' . $date_array . '];';
				echo 'var gdata = [' . $count_array . '];';
				echo 'draw_burn_js()';
				echo '</script>';
				echo '</div>';
			}


			// GRAPH FOR MAIN RELEASE

			$request_done = 'project in (CAL, ENC, SC, IP) AND issuetype in (Bug, "Documentation Task", Story, "Technical Task") AND status = Closed AND fixVersion = ' . $tag;
			$request_inprogress = 'project in (CAL, ENC, SC, IP) AND issuetype in (Bug, "Documentation Task", Story, "Technical Task") AND status in ("In Progress", Returned, "Ready for Test", "In Test", "Ready for Acceptance", Waiting, "Ready for development") AND fixVersion = ' . $tag;
			$request_todo = 'project in (CAL, ENC, SC, IP) AND issuetype in (Bug, "Documentation Task", Story, "Technical Task") AND status = Open AND fixVersion = ' . $tag;
      //
			draw_graph($request_done, $request_inprogress, $request_todo, $username, $password, "Release", "Whole Release Progress", 10, 12);

			// GRAPHS FOR EPICS


			foreach ($epics as $key => $value) {
				$request_done = 'project in (CAL, ENC, SC, IP) AND issuetype = Story AND status = Closed AND fixVersion = ' . $tag . ' AND cf[11090] = ' . $value;
				$request_inprogress = 'project in (CAL, ENC, SC, IP) AND issuetype = "Documentation Task" AND status in ("In Progress", Returned, "Ready for Test", "In Test", "Ready for Acceptance", Waiting, "Ready for development") AND fixVersion = ' . $tag . ' AND cf[11090] = ' . $value;
				$request_todo = 'project in (CAL, ENC, SC, IP) AND issuetype = "Technical Task" AND status = Open AND fixVersion = ' . $tag . ' AND cf[11090] = ' . $value;

				draw_graph($request_done, $request_inprogress, $request_todo, $username, $password, $value, issue_title($value, $username, $password), 15, 6);

			}

			// BURNDONW CHART

			draw_burn("/var/www/html/charts/data/mt_stats_inout.txt", "Burndown Chart1", "Burndown Chart1", 6);
			draw_burn("/var/www/html/charts/data/mt_stats_inout.txt", "Burndown Chart2", "Burndown Chart2", 6);

			// $xx = issues_count_hours('"Discovered During" = customer AND status = "In Test"',$username,$password);
			// echo $xx;
		?>

	</div>
</body>
</html>
