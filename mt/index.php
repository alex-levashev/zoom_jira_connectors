<!DOCTYPE html>
<html lang="en">

<head>
<title>Maintenace Statistics</title>
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
    <script src="js/bootstrap.min.js.1"></script>
    <script src="js/bootstrap.js"></script>

</head>

  <?php

    if (file_exists('service/config/config.php')) {
      require_once("service/config/config.php");
    } else {
      require_once("config/config.php");
    }

    if (file_exists('int/teams_rates.php')) {
  		require_once("int/teams_rates.php");
  	} else {
      echo 'No file with rates!';
    }

    if (file_exists('procedures/proc.php')) {
      require_once("procedures/proc.php");
    } else {
      echo 'No file with procedures!';
    }

    ?>

<!-- PAGE STARTED -->

<body>
  <div class="container col-sm-12">
    <div class="page-header">
      <h3 class="text-center">Maintenance Statistics</h3>
    </div>
  </div>
  <div>
    <ul class="nav nav-tabs nav-justified nav-pills">
      <li class="active"><a data-toggle="tab" href="#mt_daily">Mainteance Daily</a></li>
      <li><a data-toggle="tab" href="#mt_inout">Mainteance In/Out</a></li>
      <li><a data-toggle="tab" href="#lumierepropilgrim">Lumiere Pro/Pilgrim</a></li>

      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Misc<span class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-right">
          <?php if (file_exists('int/invoices_count.php')) { echo '<li><a href="int/invoices_count.php" target="_blank">Invoice</a></li>'; } ?>
          <?php if (file_exists('int/invoices_count_customer_issues_fte.php')) { echo '<li><a href="int/invoices_count_customer_issues_fte.php" target="_blank">Invoice - Customer Issues + FTE</a></li>'; } ?>
          <li><a href="http://devops.office.zoomint.com/mt-report/" target="_blank">Maintenance Report</a></li>
          <?php if (file_exists('int/timelog_difference.php')) { echo '<li><a href="int/timelog_difference.php" target="_blank">Timelog Difference</a></li>'; } ?>
          <?php if (file_exists('int/internals_time_per_month.php')) { echo '<li><a href="int/internals_time_per_month.php" target="_blank">Internals - Timelog</a></li>'; } ?>
          <?php if (file_exists('int/user_time_per_issue.php')) { echo '<li><a href="int/user_time_per_issue.php" target="_blank">Users Time Per Issue</a></li>'; } ?>
          <?php if (file_exists('int/filter_time_split.php')) { echo '<li><a href="int/filter_time_split.php" target="_blank">Time Split For Filter</a></li>'; } ?>
          <li><a href="mt_time_split.php" target="_blank">Maintenance Time Split</a></li>
          <li><a href="int/issues_count_by_version.php" target="_blank">Issues Count By Version</a></li>
        </ul>
      </li>
    </ul>

  <div class="tab-content">
    <div id="mt_daily" class="tab-pane fade in active">
      <h3 class="text-center">Maintenance Daily Statistics</h3>
      <?php
      MTIssuesGraphFromDB($dbservername, $dbusername, $dbpassword, $dbname, 'MT_Daily', 'CheckDate', 'Count1', ' - Main Maintenace Metric', '', '', '', '', '', '', "MainMT", "Main MT Metric", 12, "35%", date("Y-m-d", strtotime("-1 months")), date("Y-m-d"));
      ?>
    </div>

    <div id="mt_inout" class="tab-pane fade">
      <h3 class="text-center">Maintenance In/Out Statistics</h3>
      <?php
      BarGraphFromDB($dbservername, $dbusername, $dbpassword, $dbname, 'InOutStats', 'CheckDate', 'Count1', ' - In', 'Count2', ' - Out', '', '', '', '', "InOutStats", "InOutStats", 12, "35%", date("Y-m-d", strtotime("-2 months")), date("Y-m-d"));
      ?>
    </div>

    <div id="lumierepropilgrim" class="tab-pane fade">
      <h3 class="text-center">Lumiere Pro Statistics</h3>
      <?php
      BarGraphFromDB($dbservername, $dbusername, $dbpassword, $dbname, 'LumierePro_Pilgrim_Stats', 'CheckDate', 'Count1', ' - Pilgrim', 'Count2', ' - LumierePro', '', '', '', '', "LumierePilgrimTime", "LumierePro/Pilgrim Time", 12, "35%", date("Y-m-d", strtotime("-1 months")), date("Y-m-d"));
      ?>
    </div>
  </div>

</div>
</body>
</html>
