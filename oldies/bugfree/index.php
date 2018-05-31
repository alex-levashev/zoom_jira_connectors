	<!DOCTYPE html>
<html lang="en">

<head>
	<title>BugFreeSW Q4 - 2017 Statistics</title>
	<link rel="shortcut icon" href="favicon.gif" type="image/gif">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
			<link href="css/bootstrap.css" rel="stylesheet">
			<link href="css/bootstrap.css" rel="stylesheet">
			<script src="js/tether.min.js"></script>
			<script src="js/jquery.min.js"></script>
			<script src="js/bootstrap.min.js"></script>
			<script src="js/bootstrap.js"></script>
			<script src="js/bootstrap.min.js.1"></script>
</head>

<body>
	<script type="text/javascript">
		$(document).ready(function() {
	    	$('dropdown-toggle').dropdown()
			});
	</script>

	<div class="container col-sm-12">
	  <div class="page-header">
			<h1 class="text-center">BugFreeSW Q4 2017 Statistics</h1>

	<!-- ENCOURAGE -->
		<h3>BugFreeSW Q4 2017 Statistics</h3>
		<div class="well well-sm">
			<?php
				date_default_timezone_set("Europe/Prague");
				$filename = 'data/bugfreesw_stats.txt';
				if (file_exists($filename)) {
	    		echo "<b>Last updated : </b>" . date ("F d Y H:i:s.", filemtime($filename));
				}
			?>
		</div>
		<p>
			<?php
				function jj_readcsv($filename, $header=false) {
					$handle = fopen($filename, "r");
					if ($header) {
						$csvcontents = fgetcsv($handle);
						echo '<table class="table text-center table-striped table-hover">';
						echo '<tr>';
						foreach ($csvcontents as $headercolumn) {
							echo "<th width='10%'><div class='text-center'>$headercolumn</div></th>";
						}
						echo '</tr>';
						echo '</table>';
					}

					echo '<div style="overflow: auto;">';
					echo '<table class="table text-center table-striped table-hover">';
					while ($csvcontents = fgetcsv($handle)) {
						echo '<tr>';
						foreach ($csvcontents as $column) {
							echo "<td width='10%'>$column</td>";
						}
						echo '</tr>';
					}
					echo '</table>';
					echo '</div>';

					fclose($handle);
				}

				jj_readcsv('data/bugfreesw_stats.txt',true);

						?>
		</p>
		</div>
	</div>
<div>
<h2><center>BugFreeSW Q4 was ended 18th Dec 2017 with results:</center></h2></br>
<h3><center>5 "L" - sized bugs were resolved</br>
4 "M" - sized bugs were resolved</br>
4 "S" - sized bugs were resolved</br>
</br>
Budget spent - 75 000 CZK</center></h3>
</div>

</body>
</html>
