	<!DOCTYPE html>
<html lang="en">

<head>
	<title>Release Info Page</title>
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
			$tag = 'ZOOM-6.4.0';
			$epics = ['ENC-3895', 'ENC-3802', 'CAL-17845', 'SC-10346', 'ENC-3961', 'DEVOPS-1642'];
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
			          beginAtZero: true,
			        }
			      }],
			      xAxes: [{
			        stacked: true,
			        ticks: {
			          beginAtZero: true,
								max: d,
								stepSize: e
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
			      label: "Days",
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
			          },
								max: '2018-08-16'
			        }
			      }],
						yAxes:[{
                ticks: {
                    suggestedMin: 0,
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

				$t_shirt_sizes_scopic = array( "S"=>"2", "M"=>"7", "L"=>"14", "XL"=>"30","XXL"=>"90");
				$t_shirt_sizes_baz = array( "S"=>"2", "M"=>"5", "L"=>"10", "XL"=>"20","XXL"=>"80");
				$t_shirt_sizes_ua = array( "S"=>"3", "M"=>"10", "L"=>"20", "XL"=>"45","XXL"=>"100");
				$t_shirt_sizes_enc = array( "S"=>"2", "M"=>"5", "L"=>"10", "XL"=>"20","XXL"=>"50");
				$t_shirt_sizes_devops = array( "S"=>"5", "M"=>"9", "L"=>"13", "XL"=>"24","XXL"=>"56");

				foreach($issue_list_decoded->issues as $i) {
					$t_shirt_size_letter = $i->fields->customfield_11506->value;
					$labels_array = $i->fields->labels;
					foreach ($labels_array as $value) {
							if ( $value == "scopic" ) {
								$t_shirt_size_value = $t_shirt_sizes_scopic[$t_shirt_size_letter];
							} elseif ( $value == "Team:TheBazaar" ) {
								$t_shirt_size_value = $t_shirt_sizes_baz[$t_shirt_size_letter];
							} elseif ( $value == "Team:UA" ) {
								$t_shirt_size_value = $t_shirt_sizes_ua[$t_shirt_size_letter];
							} elseif ( $value == "Team:ENC" ) {
								$t_shirt_size_value = $t_shirt_sizes_enc[$t_shirt_size_letter];
							} elseif ( $value == "devops" ) {
								$t_shirt_size_value = $t_shirt_sizes_devops[$t_shirt_size_letter];
							}
					}

					$t_shirt_sizes_total_value += $t_shirt_size_value;
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

			function draw_graph($filter1, $filter2, $filter3, $username, $password, $graph_name, $graph_label, $height, $cols, $stepsize) {
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
				echo 'var d = ' . (intval($count1) + intval($count2) + intval($count3)) . ';';
				echo 'var e = "' . $stepsize . '";';
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

			$request_done = 'issuetype in (Bug, "Documentation Task", Story, "Technical Task") AND status = Closed AND fixVersion = ' . $tag;
			$request_inprogress = 'issuetype in (Bug, "Documentation Task", Story, "Technical Task") AND status in ("In Progress", Returned, "Ready for Test", "In Test", "Ready for Acceptance", Waiting) AND fixVersion = ' . $tag;
			$request_todo = 'issuetype in (Bug, "Documentation Task", Story, "Technical Task") AND status in ("Open", "Ready for development") AND fixVersion = ' . $tag;

			draw_graph($request_done, $request_inprogress, $request_todo, $username, $password, "Release", "Whole Release Progress", 10, 12, 10);

			// GRAPHS FOR EPICS

			foreach ($epics as $key => $value) {

				$request_done = 'issuetype in (Bug, "Documentation Task", Story, "Technical Task") AND status = Closed AND fixVersion = ' . $tag . ' AND cf[11090] = ' . $value;
				$request_inprogress = 'issuetype in (Bug, "Documentation Task", Story, "Technical Task") AND status in ("In Progress", Returned, "Ready for Test", "In Test", "Ready for Acceptance", Waiting) AND fixVersion = ' . $tag . ' AND cf[11090] = ' . $value;
				$request_todo = 'issuetype in (Bug, "Documentation Task", Story, "Technical Task") AND status in ("Open", "Ready for development") AND fixVersion = ' . $tag . ' AND cf[11090] = ' . $value;

				draw_graph($request_done, $request_inprogress, $request_todo, $username, $password, $value, issue_title($value, $username, $password), 15, 6, 1);

			}

			// BURNDONW CHART

			draw_burn("/var/www/html/release/data/burndown.txt", "Burndown Chart - Release 6.4.0", "Burndown Chart - Release 6.4.0", 12);

		?>

	</div>
</body>
</html>
