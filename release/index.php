<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<title>Dashboard MT/Release Info</title>
<style type="text/css">
body, html { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; }
iframe { border: none; width: 100%; height: 100%; display: none; }
iframe.active { display: block;}
</style>
<script src="js/jquery-latest.min.js" type="text/javascript"></script>
<script type="text/javascript">
var Dash = {
    nextIndex: 0,
    //Don't put too many items in this list
    dashboards: [
        {url: "release.php", time: 60, refresh: true},
        {url: "mt.php", time: 60, refresh: true},
    ],
    startup: function () {
        for (var index = 0; index < Dash.dashboards.length; index++) {
						Dash.loadFrame(index);
				}
        setTimeout(Dash.display, Dash.dashboards[0].time * 1000);
    },
    loadFrame: function (index) {
				var iframe = document.getElementById(index);
				iframe.src = Dash.dashboards[index].url;
    },
    display: function () {
        var dashboard = Dash.dashboards[Dash.nextIndex];
				Dash.hideFrame(Dash.nextIndex - 1);
				if (dashboard.refresh) {
						Dash.loadFrame(Dash.nextIndex);
				}
				Dash.showFrame(Dash.nextIndex);
        Dash.nextIndex = (Dash.nextIndex + 1) % Dash.dashboards.length;
        setTimeout(Dash.display, dashboard.time * 1000);
    },
    hideFrame: function (index) {
				if (index < 0) {
						index = Dash.dashboards.length - 1;
				}
				$('#'+index).css({opacity: 1.0, visibility: "visible"}).animate({opacity: 0.0},2000);
				setTimeout(function() {true;},2000);
				document.getElementById(index).removeAttribute('class');
    },
    showFrame: function (index) {
				$('#'+index).css({opacity: 0.0, visibility: "visible"}).animate({opacity: 1.0},200);
				document.getElementById(index).setAttribute('class', 'active');
    }
};
function fetchPage(url) {
    $.ajax({
        type: "GET",
        url: url,
        error: function(request, status) {
            alert('Error fetching ' + url);
        },
        success: function(data) {
            parse_hadoop_active_nodes(data.responseText);
        }
    });
}
function parse(data) {
    alert($(data).find("#nodes").text());
}
window.onload = Dash.startup;
</script>
</head>
<body>
<iframe id="0" class="active"></iframe>
<iframe id="1"></iframe>
<iframe id="2"></iframe>
<iframe id="3"></iframe>
</body>
</html>
