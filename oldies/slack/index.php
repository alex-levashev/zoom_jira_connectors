<html>
<head>
		<title>Reminders</title>
		<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
		<link rel="shortcut icon" href="../favicon.gif" type="image/gif">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/bootstrap.css" rel="stylesheet">
		<script src="js/tether.min.js"></script>
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>

</head>

<body>
<div>
<?php
$token = 'xoxp-4036454200-65004929894-364807004070-1980e4598dcb2915cae5a125138a2a67';
$api = 'https://slack.com/api/';

function GetReminderList ($token, $api) {
  $url = $api . "reminders.list?token=" . $token;
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

  $response = json_decode(curl_exec($curl), true);

  // print_r($response);
  // echo '<br>';
  // echo '<br>';

  echo '<table class="table table-bordered" cellspacing="0" width="100%">';
  echo '<thead class="bg-success"><tr>';
		echo '<th>Reminder</th>';
    echo '<th>User</th>';
		echo '<th>Date</th>';
	echo '</thead></tr>';
	echo "<tbody>";

  foreach($response["reminders"] as $item) {
    if($item['complete_ts'] == 0) {
      $items = explode("/", substr($item['text'], 35));
      $channel = $items[0];
      $messageid = substr($items[1], 1, 10) . '.' . substr($items[1], 11);
      $url = $api . "conversations.history?token=" . $token . '&channel=' . $channel . '&inclusive=ture&latest=' . $messageid . '&limit=1&pretty=1';
      curl_setopt($curl, CURLOPT_URL, $url);
      $response_message = json_decode(curl_exec($curl), true);

      $url = $api . "users.info?token=" . $token . '&user=' . $item['user'] . '&pretty=1';
      curl_setopt($curl, CURLOPT_URL, $url);
      $user_info = json_decode(curl_exec($curl), true);

      // echo '<tr><td>' . $response_message['messages'][0]['text'] . '</td><td>' . $user_info['user']['real_name'] . '</td><td>' . date("Y-m-d H:i",$item['time']+7200) . '</td></tr>';
      echo '<tr><td>' . date("Y-m-d H:i",$item['time']+7200) . '</td><td>' . $user_info['user']['real_name'] . '</td><td>' . $response_message['messages'][0]['text'] . '</td></tr>';
    }
  }


  echo "</tbody>";
	echo "</table>";

}
echo '<div class="well"><h1>Alexey levashev - Reminders</h1></div>';
GetReminderList($token, $api);


?>
</div>
</body>
</html>
