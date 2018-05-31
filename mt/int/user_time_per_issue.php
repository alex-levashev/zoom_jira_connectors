<html>
<head>
		<title>Hour of each person</title>
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
	<div class="container col-sm-12">
		<div class="page-header">
			<h3 class="text-center">User time per issue</h3>
		</div>
	</div>
	<div class="container col-sm-12">
	<form>
		<br>
		<div class="form-group">
			<!-- <div class="input-group">
				<span class="input-group-addon" id="usernameid">User Name</span>
				<input type="uname" name="uname" class="form-control" placeholder="Username" aria-describedby="usernameid">
			</div>
			<br> -->
			<div class="input-group">
				<span class="input-group-addon" id="jiranameid">Jira Issue Identifier</span>
				<input type="jname" name="jname" class="form-control" placeholder="Jira Issue Identifier" aria-describedby="jiranameid">
			</div>
		</div>
		<br>
		<center><button type="submit" class="btn btn-primary">Submit</button></center>
	</form>
	<?php

	if (file_exists('../service/config/config.php')) {
		require_once("../service/config/config.php");
	} else {
		require_once("../config/config.php");
	}

	$jkey = $_GET['jname'];
	$url = 'http://jira:81/rest/api/2/issue/' . $jkey . '/worklog';

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);


	$worklog = json_decode(curl_exec($curl), true);

	$user_in_issue = [];
	$time_spent = 0;
	foreach ($worklog['worklogs'] as $i) {
		$author_nick = $i['author']['name'];
		$author_full = $i['author']['displayName'];
		$logged_time = $i['timeSpentSeconds'];
		if (!array_key_exists($author_nick, $user_in_issue)) {
			$user_in_issue[$author_full] = $user_in_issue[$author_full] + $logged_time;
		} else {
			$user_in_issue[$author_full] = $logged_time;
		}
		// if ($author_nick == $_GET['uname']) {
		// 	$time_spent += $logged_time/3600;
		// 	$author_full = $i['author']['displayName'];
		// }
	}
	echo '<center><h4>';
	$total = 0;
	foreach ($user_in_issue as $key => $value) {
		echo '<br>' . $key . ' spent ' . $value/3600 . ' hours on ' . $jkey . '<br>';
		$total += $value;
	}
		echo '<br> Total time spend on ' . $jkey . ' is ' . $total/3600 . ' hours <br>';

	echo '</center></h4>';
	?>
	</div>
</body>
</html>
