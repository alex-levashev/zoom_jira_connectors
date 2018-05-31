<?php

if (file_exists('/var/www/html/mt/int/teams_rates.php')) {
  require_once("/var/www/html/mt/int/teams_rates.php");
} else {
  echo 'No file with rates!';
}

$fp = fopen('/var/www/html/mt/int/teams_rates.json', 'w');
fwrite($fp, json_encode($zoom_team));
fclose($fp);

  ?>
