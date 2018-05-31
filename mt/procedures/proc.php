<?php
function MTIssuesGraphFromDB($servername,
                        $username,
                        $password,
                        $dbname,
                        $tablename,
                        $fieldname0,
                        $fieldname1,
                        $fieldname1_add,
                        $fieldname2,
                        $fieldname2_add,
                        $fieldname3,
                        $fieldname3_add,
                        $fieldname4,
                        $fieldname4_add,
                        $graph_name,
                        $graph_label,
                        $cols,
                        $height,
                        $GraphStartDate,
                        $GraphEndDate) {
  $conn = new mysqli($servername, $username, $password, $dbname);
  // // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  $sql = 'SELECT * FROM ' . $tablename;
  $result = $conn->query($sql);
  $data_array = [];
  if ($result->num_rows > 0) {
    while($row = $result->fetch_array()) {
      $rows[] = $row;
    }
  } else {
      echo "0 results";
    }


  foreach($rows as $row) {
    if( $row[$fieldname0] >= $GraphStartDate && $row[$fieldname0] <= $GraphEndDate ) {
      if($fieldname0 != '') {
        if($date_array[$fieldname0] == '') {
          $date_array[$fieldname0] = '"' . $row[$fieldname0] . '"';
        } else {
          $date_array[$fieldname0] = $date_array[$fieldname0] . ', "' . $row[$fieldname0] . '"';
        }
      }
      if($fieldname1 != '') {
        if($date_array[$fieldname1] == '') {
          $date_array[$fieldname1] = '"' . $row[$fieldname1] . '"';
        } else {
          $date_array[$fieldname1] = $date_array[$fieldname1] . ', "' . $row[$fieldname1] . '"';
        }
      }
      if($fieldname2 != '') {
        if($date_array[$fieldname2] == '') {
          $date_array[$fieldname2] = '"' . $row[$fieldname2] . '"';
        } else {
          $date_array[$fieldname2] = $date_array[$fieldname2] . ', "' . $row[$fieldname2] . '"';
        }
      }
      if($fieldname3 != '') {
        if($date_array[$fieldname3] == '') {
          $date_array[$fieldname3] = '"' . $row[$fieldname3] . '"';
        } else {
          $date_array[$fieldname3] = $date_array[$fieldname3] . ', "' . $row[$fieldname3] . '"';
        }
      }
      if($fieldname4 != '') {
        if($date_array[$fieldname4] == '') {
          $date_array[$fieldname4] = '"' . $row[$fieldname4] . '"';
        } else {
          $date_array[$fieldname4] = $date_array[$fieldname4] . ', "' . $row[$fieldname4] . '"';
        }
      }
    }
  }

  // echo $fieldname0 . ' ==> ' . $date_array[$fieldname0] . '<br>';
  // echo $fieldname1 . ' ==> ' . $date_array[$fieldname1] . '<br>';
  // echo $fieldname2 . ' ==> ' . $date_array[$fieldname2] . '<br>';
  // echo $fieldname3 . ' ==> ' . $date_array[$fieldname3] . '<br>';
  // echo $fieldname4 . ' ==> ' . $date_array[$fieldname4] . '<br>';

  $conn->close();

  echo "<script>";

  echo 'function draw_graph_js() {';
    echo 'var config = {';
      echo 'type: "line",';
      echo 'data: {';
        echo 'labels: glabels,';
        echo 'datasets: [';

          echo '{';
            echo 'label: "' . $fieldname1 . $fieldname1_add . '",';
            echo 'data: gdata1,';
            echo 'borderColor: "green",';
            echo 'backgroundColor: "white",';
            echo 'fill: false,';
          echo '},';

          if($fieldname2 != '') {
            echo '{';
              echo 'label: "' . $fieldname2 . $fieldname2_add . '",';
              echo 'data: gdata2,';
              echo 'borderColor: "red",';
              echo 'backgroundColor: "white",';
              echo 'fill: false,';
            echo '},';
          }

          if($fieldname3 != '') {
            echo '{';
              echo 'label: "' . $fieldname3 . $fieldname3_add . '",';
              echo 'data: gdata3,';
              echo 'borderColor: "blue",';
              echo 'backgroundColor: "white",';
              echo 'fill: false,';
            echo '},';
          }

          if($fieldname4 != '') {
            echo '{';
              echo 'label: "' . $fieldname4 . $fieldname4_add . '",';
              echo 'data: gdata4,';
              echo 'borderColor: "yellow",';
              echo 'backgroundColor: "white",';
              echo 'fill: false,';
            echo '},';
          }

      echo ']';
      echo '},';
      echo 'options: {';
        echo 'scales: {';
          echo 'xAxes: [{';
            echo 'ticks: {';
              echo 'autoSkip: false,';
              echo 'maxRotation: 90,';
              // echo 'callback: function(value, index, values) {';
              //       echo 'var thisdate = new Date(value);';
              //       echo 'var thisday = thisdate.getDay();';
              //           echo 'if ( (thisday == 6) || (thisday == 0) ) {';
              //             echo 'values[index].major = true;';
              //             echo 'return value;';
              //           echo '} else {';
              //             echo 'return value;';
              //           echo '}';
              //       echo '},';
              echo 'callback: function(value, index, values) {';
                      echo 'var thisdate = new Date(value);';
                      echo 'var thisday = thisdate.getDay();';
                        echo 'if ( (thisday == 6) || (thisday == 0) ) {';
                          echo 'values[index].major = true;';
                          echo 'return "[ " + value + " ]";';
                        echo '} else {';
                          echo 'return value;';
                        echo '}';
                  echo '},';
              echo 'minRotation: 90,';
              // echo 'major: {';
              //   echo 'fontColor: "grey"';
              // echo '},';
              // echo 'minor: {';
              //   echo 'fontColor: "red"';
              // echo '},';
            echo '},';
            echo 'type: "time",';
            echo 'time: {';
              echo 'unit: "day",';
              echo 'unitStepSize: 1,';
              echo 'displayFormats: {';
                echo '"millisecond": "YYYY-MM-DD",';
                echo '"second": "YYYY-MM-DD",';
                echo '"minute": "YYYY-MM-DD",';
                echo '"hour": "YYYY-MM-DD",';
                echo '"day": "YYYY-MM-DD",';
                echo '"week": "YYYY-MM-DD",';
                echo '"month": "YYYY-MM-DD",';
                echo '"quarter": "YYYY-MM-DD",';
                echo '"year": "YYYY-MM-DD",';
              echo '},';
            echo '}';
          echo '}],';
          echo 'yAxes:[{';
              echo 'ticks: {';
                  echo 'suggestedMin: 0,';
              echo '}';
          echo '}],';
        echo '},';
      echo '}';
    echo '};';

    echo 'var ctx = document.getElementById(gburn).getContext("2d");';
    echo 'new Chart(ctx, config);';
  echo '}';
  echo '</script>';
  echo '<div class="container col-sm-' . $cols . '">';
  echo '<h5 class="text-center">' . $graph_label . '</h5>';
  echo '<canvas id="' . $graph_name . '" width="100%" height="' . $height . '"></canvas>';
  echo '<script>';
  echo 'var gburn = "' . $graph_name . '";';
  echo 'var glabels = [' . $date_array[$fieldname0] . '];';

  if($fieldname1 != '') {
    echo 'var gdata1 = [' . $date_array[$fieldname1] . '];';
  }
  if($fieldname2 != '') {
    echo 'var gdata2 = [' . $date_array[$fieldname2] . '];';
  }
  if($fieldname3 != '') {
    echo 'var gdata3 = [' . $date_array[$fieldname3] . '];';
  }
  if($fieldname4 != '') {
    echo 'var gdata4 = [' . $date_array[$fieldname4] . '];';
  }
  echo 'draw_graph_js()';
  echo '</script>';
  echo '</div>';
}

function JiraDecodedJson($url, $username, $password) {
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
  $result = json_decode(curl_exec($curl), true);
  return ($result);
}

function MTTimeCalculation($zoom_team, $username, $password, $cols, $graph_name, $pie_name, $graph_label, $FilterStartDate, $FilterEndDate, $height) {

  $request = 'worklogDate >= ' . $FilterStartDate . ' AND worklogDate <= ' . $FilterEndDate;
  $url = 'http://jira:81/rest/api/2/search?jql=' . urlencode($request) . '&maxResults=1000';

  $int_teams_mt_time = 0;
  $int_teams_dev_time = 0;

  $issues = JiraDecodedJson($url, $username, $password);

  foreach ($issues['issues'] as $issue) {
    $customer_flag = 0;
    if($issue['fields']['customfield_10791']['value'] == 'customer') {
      $customer_flag = 1;
    }
    $key = $issue['key'];
    $worklog = JiraDecodedJson("http://jira:81/rest/api/2/issue/$key/worklog", $username, $password);
    foreach ($worklog['worklogs'] as $i) {
      if( array_key_exists($i['author']['name'], $zoom_team) && strtotime(substr($i['started'], 0, 10)) >= strtotime($FilterStartDate) && strtotime(substr($i['started'], 0, 10)) <= strtotime($FilterEndDate)) {
        $team_list[$zoom_team[$i['author']['name']]['team']]['all'] += $i['timeSpentSeconds']/3600;
        //IF NEED TO KNOW THE NAMES FOR EACH COLUMNS
        // if($zoom_team[$i['author']['name']]['team'] == 'NA') {
        //   echo $i['author']['name'] . '<br>';
        // }
        if(($zoom_team[$i['author']['name']]['location'] == 'In') && ($customer_flag == 1)) {
          $int_teams_mt_time += $i['timeSpentSeconds']/3600;
        }
        if(($zoom_team[$i['author']['name']]['location'] == 'In') && ($customer_flag == 0)) {
          $int_teams_dev_time += $i['timeSpentSeconds']/3600;
        }
        if ( $customer_flag == 1 ) {
          $team_list[$zoom_team[$i['author']['name']]['team']]['mt'] += $i['timeSpentSeconds']/3600;
        }
      }
    }
  }

  foreach ($team_list as $key => $value) {
    $regular_dev = $value['all'] - $value['mt'];
    $string_all .= '"' . $regular_dev . '",';
    $string_mt .= '"' . $value['mt'] . '",';

  }

  echo '<script>';
  echo 'function draw_bar_js() {';
    echo 'var ctx = document.getElementById(gn);';
    echo 'var myChart = new Chart(ctx, {';
      echo 'type: "bar",';
      echo 'data: {';
        echo 'labels: [' . '"' . implode('", "', array_keys($team_list)) . '"'  . '], ';
        echo 'datasets: [';
          echo '{ label: "Regular development", data: [' . rtrim($string_all,',') . '], backgroundColor: "green" },';
          echo '{ label: "Maintenance", data: [' . rtrim($string_mt,',') . '], backgroundColor: "yellow" },';
        echo ']';
      echo '},';
      echo 'options: { scales: { yAxes: [{ stacked: true, ticks: { beginAtZero: true,} }], xAxes: [{ stacked: true, ticks: { beginAtZero: true } }] } }';
    echo '});';
  echo '}';
  echo 'function draw_pie_js() {';
    echo 'var ctx = document.getElementById(pn);';
    echo 'var myChart = new Chart(ctx, {';
      echo 'type: "pie",';
      echo 'data: {';
        echo 'labels: ["Development", "Maintenance"],';
        echo 'datasets: [{';
          echo 'label: "Hours",';
          echo 'backgroundColor: ["#3e95cd", "#8e5ea2"],';
          echo 'data: [' . $int_teams_dev_time . ', ' . $int_teams_mt_time . ']';
        echo '}]';
      echo '},';
      echo 'options: {';
        echo 'title: {';
          echo 'display: true,';
          echo 'text: "Split by Development/Maintenance for internal teams"';
        echo '}';
      echo '}';
    echo '});';
  echo '}';

  echo '</script>';

  echo '<div class="container col-sm-' . $cols . '">';
  echo '<h5 class="text-center">' . $graph_label . '</h5>';
  echo '<canvas id="' . $graph_name . '" width="100%" height="' . $height . '"></canvas>';
  echo '</div>';
  echo '<script>';
  echo 'var gn = "' . $graph_name . '";';
  echo 'var pn = "' . $pie_name . '";';
  echo 'draw_bar_js()';
  echo '</script>';

  echo '<div class="container col-sm-' . $cols . '">';
  echo '<h5 class="text-center">' . $graph_label . '</h5>';
  echo '<canvas id="' . $pie_name . '" width="100%" height="' . $height . '"></canvas>';
  echo '</div>';
  echo '<script>';
  echo 'var pn = "' . $pie_name . '";';
  echo 'draw_pie_js()';
  echo '</script>';

}

function DateTimePick () {
  global $FilterStartDate, $FilterEndDate;
  echo '<div class="container col-sm-12">';
  			echo '<form>';
  			echo '<div class="col-sm-3"></div>';
  	    echo '<div class="col-sm-3">';
  	        echo '<div class="form-group">';
  	            echo '<div class="input-group date" id="datetimepicker6">';

                if ($_GET["start"] == '') {
                  $FilterStartDate = date('Y-m-d');
                }
                else {
                  $FilterStartDate = $_GET["start"];
                }

  	                echo '<input type="text" class="form-control" name="start" type="start" value="' . $FilterStartDate .'" />';
  	                echo '<span class="input-group-addon">';
  	                    echo '<span class="glyphicon glyphicon-calendar"></span>';
  	                echo '</span>';
  	            echo '</div>';
  	        echo '</div>';
  	    echo '</div>';
  	    echo '<div class="col-sm-3">';
  	        echo '<div class="form-group">';
  	            echo '<div class="input-group date" id="datetimepicker7">';

                if ($_GET["end"] == '') {
                  $FilterEndDate = date('Y-m-d');
                }
                else {
                  $FilterEndDate = $_GET["end"];
                }

  	                echo '<input type="text" class="form-control" name="end" type="end" value="' . $FilterEndDate .'" />';
  	                echo '<span class="input-group-addon">';
  	                    echo '<span class="glyphicon glyphicon-calendar"></span>';
  	                echo '</span>';
  	            echo '</div>';
  	        echo '</div>';
  	    echo '</div>';
        echo '<div class="col-sm-1">';
  			   echo '<center><button type="submit" class="btn btn-primary">Submit</button></center>';
        echo '</div>';
        echo '<div class="col-sm-2"></div>';
  			echo '</form>';
    echo '</div>';

  	echo '<script type="text/javascript">';
  	    echo '$(function () {';
  	        echo '$("#datetimepicker6").datetimepicker({';
  						echo 'format: "YYYY-MM-DD"';
  					echo '});';
  	        echo '$("#datetimepicker7").datetimepicker({';
  	            echo 'useCurrent: false,';
  							echo 'format: "YYYY-MM-DD"';
  	        echo '});';
  	        echo '$("#datetimepicker6").on("dp.change", function (e) {';
  	            echo '$("#datetimepicker7").data("DateTimePicker").minDate(e.date);';
  	        echo '});';
  	        echo '$("#datetimepicker7").on("dp.change", function (e) {';
  	            echo '$("#datetimepicker6").data("DateTimePicker").maxDate(e.date);';
  	        echo '});';
  	    echo '});';
  	echo '</script>';
}

function convertCurrency($amount, $from, $to) {
  $xml=simplexml_load_file("https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml") or die("Error: Cannot create object");
  $currency1 = $xml->Cube->Cube->Cube[0]->attributes()->currency;
  $currency2 = $xml->Cube->Cube->Cube[3]->attributes()->currency;
  if($currency1=='USD' & $currency2 == 'CZK') {
    $EURUSD = $xml->Cube->Cube->Cube[0]->attributes()->rate;
    $EURCZK = $xml->Cube->Cube->Cube[3]->attributes()->rate;
    $amount_usd = $amount/floatval($EURCZK)*floatval($EURUSD);
    return round($amount_usd,2);
  } else {
    return 0;
  }
}

function BarGraphFromDB($servername,
                        $username,
                        $password,
                        $dbname,
                        $tablename,
                        $fieldname0,
                        $fieldname1,
                        $fieldname1_add,
                        $fieldname2,
                        $fieldname2_add,
                        $fieldname3,
                        $fieldname3_add,
                        $fieldname4,
                        $fieldname4_add,
                        $graph_name,
                        $graph_label,
                        $cols,
                        $height,
                        $GraphStartDate,
                        $GraphEndDate) {
  $conn = new mysqli($servername, $username, $password, $dbname);
  // // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  $sql = 'SELECT * FROM ' . $tablename;
  $result = $conn->query($sql);
  $data_array = [];
  if ($result->num_rows > 0) {
    while($row = $result->fetch_array()) {
      $rows[] = $row;
    }
  } else {
      echo "0 results";
    }


  foreach($rows as $row) {
    if( $row[$fieldname0] >= $GraphStartDate && $row[$fieldname0] <= $GraphEndDate ) {
      if($fieldname0 != '') {
        if($date_array[$fieldname0] == '') {
          $date_array[$fieldname0] = '"' . $row[$fieldname0] . '"';
        } else {
          $date_array[$fieldname0] = $date_array[$fieldname0] . ', "' . $row[$fieldname0] . '"';
        }
      }
      if($fieldname1 != '') {
        if($date_array[$fieldname1] == '') {
          $date_array[$fieldname1] = '"' . $row[$fieldname1] . '"';
        } else {
          $date_array[$fieldname1] = $date_array[$fieldname1] . ', "' . $row[$fieldname1] . '"';
        }
      }
      if($fieldname2 != '') {
        if($date_array[$fieldname2] == '') {
          $date_array[$fieldname2] = '"' . $row[$fieldname2] . '"';
        } else {
          $date_array[$fieldname2] = $date_array[$fieldname2] . ', "' . $row[$fieldname2] . '"';
        }
      }
      if($fieldname3 != '') {
        if($date_array[$fieldname3] == '') {
          $date_array[$fieldname3] = '"' . $row[$fieldname3] . '"';
        } else {
          $date_array[$fieldname3] = $date_array[$fieldname3] . ', "' . $row[$fieldname3] . '"';
        }
      }
      if($fieldname4 != '') {
        if($date_array[$fieldname4] == '') {
          $date_array[$fieldname4] = '"' . $row[$fieldname4] . '"';
        } else {
          $date_array[$fieldname4] = $date_array[$fieldname4] . ', "' . $row[$fieldname4] . '"';
        }
      }
    }
  }

  $conn->close();

  echo "<script>";

  echo 'function draw_bar_js() {';
    echo 'var config = {';
      echo 'type: "bar",';
      echo 'data: {';
        echo 'labels: glabels,';
        echo 'datasets: [';

          echo '{';
            echo 'label: "' . $fieldname1 . $fieldname1_add . '",';
            echo 'data: gdata1,';
            echo 'borderColor: "green",';
            echo 'backgroundColor: "blue",';
            echo 'fill: false,';
          echo '},';

          if($fieldname2 != '') {
            echo '{';
              echo 'label: "' . $fieldname2 . $fieldname2_add . '",';
              echo 'data: gdata2,';
              echo 'borderColor: "red",';
              echo 'backgroundColor: "red",';
              echo 'fill: false,';
            echo '},';
          }

          if($fieldname3 != '') {
            echo '{';
              echo 'label: "' . $fieldname3 . $fieldname3_add . '",';
              echo 'data: gdata3,';
              echo 'borderColor: "blue",';
              echo 'backgroundColor: "green",';
              echo 'fill: false,';
            echo '},';
          }

          if($fieldname4 != '') {
            echo '{';
              echo 'label: "' . $fieldname4 . $fieldname4_add . '",';
              echo 'data: gdata4,';
              echo 'borderColor: "yellow",';
              echo 'backgroundColor: "white",';
              echo 'fill: false,';
            echo '},';
          }

      echo ']';
      echo '},';
      echo 'options: {';
        echo 'scales: {';
          echo 'xAxes: [{';
            echo 'ticks: {';
              echo 'autoSkip: false,';
              echo 'maxRotation: 90,';
              echo 'callback: function(value, index, values) {';
                    echo 'var thisdate = new Date(value);';
                    echo 'var thisday = thisdate.getDay();';
                        echo 'if ( (thisday == 6) ||(thisday == 0) )   {';
                          echo 'return "[ " + value + " ]";';
                        echo '} else { return value; }';

                    echo '},';
              echo 'minRotation: 90';
            echo '},';
            echo 'type: "time",';
            echo 'time: {';
              echo 'unit: "day",';
              echo 'unitStepSize: 1,';
              echo 'displayFormats: {';
                echo '"millisecond": "YYYY-MM-DD",';
                echo '"second": "YYYY-MM-DD",';
                echo '"minute": "YYYY-MM-DD",';
                echo '"hour": "YYYY-MM-DD",';
                echo '"day": "YYYY-MM-DD",';
                echo '"week": "YYYY-MM-DD",';
                echo '"month": "YYYY-MM-DD",';
                echo '"quarter": "YYYY-MM-DD",';
                echo '"year": "YYYY-MM-DD",';
              echo '},';
              echo 'min: "' . date("Y-m-d", strtotime('-1 month'))  . '",';
              echo 'max: "' . date("Y-m-d")  . '"';
            echo '}';
          echo '}],';
          echo 'yAxes:[{';
              echo 'ticks: {';
                  echo 'suggestedMin: 0,';
              echo '}';
          echo '}],';
        echo '},';
      echo '}';
    echo '};';

    echo 'var ctx = document.getElementById(gburn).getContext("2d");';
    echo 'new Chart(ctx, config);';
  echo '}';
  echo '</script>';
  echo '<div class="container col-sm-' . $cols . '">';
  echo '<h5 class="text-center">' . $graph_label . '</h5>';
  echo '<canvas id="' . $graph_name . '" width="100%" height="' . $height . '"></canvas>';
  echo '<script>';
  echo 'var gburn = "' . $graph_name . '";';
  echo 'var glabels = [' . $date_array[$fieldname0] . '];';

  if($fieldname1 != '') {
    echo 'var gdata1 = [' . $date_array[$fieldname1] . '];';
  }
  if($fieldname2 != '') {
    echo 'var gdata2 = [' . $date_array[$fieldname2] . '];';
  }
  if($fieldname3 != '') {
    echo 'var gdata3 = [' . $date_array[$fieldname3] . '];';
  }
  if($fieldname4 != '') {
    echo 'var gdata4 = [' . $date_array[$fieldname4] . '];';
  }
  echo 'draw_bar_js()';
  echo '</script>';
  echo '</div>';
}


?>
