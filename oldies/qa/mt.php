	<!DOCTYPE html>
<html lang="en">

<head>
	<title>Maintenance Info Page</title>
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
			echo '<h1 class="text-center">Maintenance Info Page</h1>';
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
			var config = {
			  type: 'line',
			  data: {
			    labels: glabels,
			    datasets: [{
			      label: "MT Stats IN",
			      data: gdatain,
						borderColor: 'green',
						backgroundColor: 'white',
						fill: false,
			    },
					{
			      label: "MT Stats OUT",
			      data: gdataout,
						borderColor: 'red',
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

			var ctx = document.getElementById(gn).getContext("2d");
			new Chart(ctx, config);


		}
		</script>
		<?php

			function draw_graph_mt($file, $graph_name, $graph_label, $cols) {
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

					if($count_array_in == '') {
						$count_array_in = $value[1];
					} else {
						$count_array_in = $count_array_in . ', ' . $value[1];
					}

					if($count_array_out == '') {
						$count_array_out = $value[2];
					} else {
						$count_array_out = $count_array_out . ', ' . $value[2];
					}

				}

				echo '<div class="container col-sm-' . $cols . '">';
				echo '<h3 class="text-center">' . $graph_label . '</h3>';
				echo '<canvas id="' . $graph_name . '" width="100%" height="40%"></canvas>';
				echo '<script>';
				echo 'var gn = "' . $graph_name . '";';
				echo 'var glabels = [' . $date_array . '];';
				echo 'var gdatain = [' . $count_array_in . '];';
				echo 'var gdataout = [' . $count_array_out . '];';
				echo 'draw_graph_js()';
				echo '</script>';
				echo '</div>';
			}


			draw_graph_mt("/var/www/html/charts/data/mt_stats_inout.txt", "MTStats", "Maintenance Queue In/Out Stats", 12);
			// draw_graph_mt("/var/www/html/charts/data/mt_stats_daily.txt", "MT Stats Daily", "MT Stats Daily", 6);
			// draw_graph_mt("/var/www/html/charts/data/mt_stats.txt", "MT Stats", "MT Stats", 6);
		?>




	</div>
</body>
</html>
