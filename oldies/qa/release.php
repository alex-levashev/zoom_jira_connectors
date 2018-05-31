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
			echo '<h3 class="text-center">Issues count by Affected Version</h3>';
			?>
		</div>
		<form>
			<br>
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon" id="jiranameid">Jira Issue Identifier</span>
					<input type="filter_name" name="filter_name" class="form-control" placeholder="Jira Filter" aria-describedby="jiranameid">
				</div>
			</div>
			<br>
			<center><button type="submit" class="btn btn-primary">Submit</button></center>
		</form>
	</div>
	<div>
		<script>
		function draw_graph_js() {
			var ctx = document.getElementById(gn);
			var myChart = new Chart(ctx, {
				type: 'bar',
		    data: {
		      labels: a,
		      datasets: [
		        {
		          label: "Issues count",
		          backgroundColor: "#3e95cd",
		          data: b
		        }
		      ]
		    },
			  options: {
			    scales: {
			      yAxes: [{
			        ticks: {
			          beginAtZero: true,
			        }
			      }]
			    }
			  }
			});
		}

		</script>
		<?php

			if (file_exists('service/config/config.php')) {
				require_once("service/config/config.php");
			} else {
				require_once("config/config.php");
			}

			function issues_array_by_affected_version($request_filter,$username,$password) {

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

				// $total_issues_count = json_decode(curl_exec($curl), true)['total'];

				$versons_issues_array = [];
				foreach ($issue_list_decoded->issues as $i) {
					$affected_version = $i->fields->versions;
					foreach ($affected_version as $key) {
					$verson = $key->name;
						if(array_key_exists($verson, $versons_issues_array)) {
							$versons_issues_array[$verson] += 1;
						} else {
							$versons_issues_array[$verson] = 1;
						}
					}
				}
				ksort($versons_issues_array);
				$versions = '"' . implode('","', array_keys($versons_issues_array)) . '"';
				// $versions =   "'" . implode(", ", array_keys($versons_issues_array)) . "'";
				$versions_count = implode(",", array_values($versons_issues_array));

				draw_graph($versions, $versions_count, $username, $password, "Graph name", "Issues count by Affected Version", 30, 12);

			}

			function draw_graph($labels, $counts, $username, $password, $graph_name, $graph_label, $height, $cols) {
				echo '<div class="container col-sm-' . $cols . '">';
				echo '<h5 class="text-center">' . $graph_label . '</h5>';
				echo '<canvas id="' . $graph_name . '" width="100%" height="' . $height . '%"></canvas>';
				echo '</div>';
				echo '<script>';
				echo 'var a = [' . $labels . '];';
				echo 'var b = [' . $counts . '];';
				echo 'var gn = "' . $graph_name . '";';
				echo 'draw_graph_js()';
				echo '</script>';
			}

			$filter_name = $_GET['filter_name'];
			issues_array_by_affected_version($filter_name, $username, $password);


		?>

	</div>
</body>
</html>
