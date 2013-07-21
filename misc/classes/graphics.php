<?php
if($debug) {
	$file = __FILE__;
	debug::include_report($file);
}


class graphics extends join_edit {
  function graphics_position() {
    global $CONF, $DB, $LNG, $TMPL;


	$today = date('j');
	$days_in_month = date('t');	
	$username = $TMPL['username'];
	$query = "SELECT  *  FROM  rtt_graphics_data WHERE `username` =  '$username'";  
	//echo $query;
	$result = mysql_query($query)  or  die(mysql_error()); 

	$value = array();
	while($info = mysql_fetch_array($result))  
	{
	$username = $info['username'];
		for($i=0; $i<=$today; $i++)
		{
			$value[$i] = $info["day_{$i}_position"];
			//echo "{$value[$i]} = day_{$i}_position<br/>";
		}
	}


	$lastlogin = $time - (24*60*60*7);
	$position = mysql_query("set @i:=0;") or die(mysql_error());
	$position = mysql_query("select *,@i:=@i+1 as number from rtt_stats stats, rtt_servers servers WHERE servers.id = stats.id AND active = 1 AND `lastlogin`>'$lastlogin' AND (servers.success/servers.attemps*100) > 30  order by `score` DESC") or die(mysql_error());
		while($info = mysql_fetch_array($position))  
		{
		
			if($username == $info['username'])
			{
			$real_position = $info['number'];
			break;
			}
		}	
	if(!empty($real_position)){
	$value[$today] = $real_position;
}
	$month = mysql_fetch_row(mysql_query("SELECT last_new_month from rtt_etc"));
	$month = $month[0];
	$month = $month - 1;
	$head = <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>amCharts examples</title>
        <link rel="stylesheet" href="style.css" type="text/css">
        <script src="/t/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="/t/amcharts/raphael.js" type="text/javascript"></script>
HTML;

		for ( $i=1; $i<=$today; $i++) {
			$form = <<<HTML
			{date: new Date(2012, {$month}, {$i}),value: {$value[$i]}}
HTML;
		$help .= $form;
		if ($i<$today) {
		$help .= ',';
			}
		}
		//echo $help;
$data = <<<HTML
       <script type="text/javascript">
            var lineChartData = [{$help}];
HTML;

$foot = <<<HTML
            AmCharts.ready(function () {
                var chart = new AmCharts.AmSerialChart();
                chart.dataProvider = lineChartData;
                chart.pathToImages = "/t/amcharts/images/";
                chart.categoryField = "date";

                // sometimes we need to set margins manually
                // autoMargins should be set to false in order chart to use custom margin values
                chart.autoMargins = false;
                chart.marginRight = 0;
                chart.marginLeft = 0;
                chart.marginBottom = 0;
                chart.marginTop = 0;

                // AXES
                // category                
                var categoryAxis = chart.categoryAxis;
                categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
                categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD
                categoryAxis.inside = true;
                categoryAxis.gridAlpha = 0;
                categoryAxis.tickLength = 0;
                categoryAxis.axisAlpha = 0;
                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.dashLength = 4;
                valueAxis.axisAlpha = 0;
                chart.addValueAxis(valueAxis);

                // GRAPH
                 var graph = new AmCharts.AmGraph();
                graph.dashLength = 3;
                graph.lineColor = "#88838e";
                graph.valueField = "value";
                graph.dashLength = 3;
                graph.bullet = "round";
                chart.addGraph(graph);

                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.cursorAlpha = 0;
                chart.addChartCursor(chartCursor);

                // WRITE
                chart.write("chartdiv");
            });
        </script>
    </head>
    
    <body>
        <div id="chartdiv" style="width:100%; height:300px;"></div>
    </body>

</html>	
	
	
	
HTML;
$TMPL['user_cp_content'] .= '<br/><hr/>';
$TMPL['user_cp_content'] .= '<h3>Позиция сервера в рейтинге за текущий месяц</h3>';
$TMPL['user_cp_content'] .= '<hr/>';
$TMPL['user_cp_content'] .= $head;
$TMPL['user_cp_content'] .= $data;
$TMPL['user_cp_content'] .= $foot;
  }
  function graphics_views() {
    global $CONF, $DB, $LNG, $TMPL;


	$today = date('j');
	$days_in_month = date('t');	
	$username = $TMPL['username'];
	$query = "SELECT  *  FROM  rtt_graphics_data WHERE `username` =  '$username'";  
	//echo $query;
	$result = mysql_query($query)  or  die(mysql_error()); 

	$value = array();
	while($info = mysql_fetch_array($result))  
	{
		for($i=1; $i<=$today; $i++)
		{
			$value[$i] = $info["day_{$i}_views"];
		}
	}	
	

	$month = mysql_fetch_row(mysql_query("SELECT last_new_month from rtt_etc"));
	$month = $month[0];
	$month = $month - 1;
	$head = <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>amCharts examples</title>
        <link rel="stylesheet" href="style.css" type="text/css">
        <script src="/t/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="/t/amcharts/raphael.js" type="text/javascript"></script>
HTML;

		for ( $i=1; $i<=$today; $i++) {
			$form = <<<HTML
			{date: new Date(2012, {$month}, {$i}),value: {$value[$i]}}
HTML;
		$help .= $form;
		if ($i<$today) {
		$help .= ',';
			}
		}
		//echo $help;
$data = <<<HTML
       <script type="text/javascript">
            var lineChartData = [{$help}];
HTML;

$foot = <<<HTML
            AmCharts.ready(function () {
                var chart = new AmCharts.AmSerialChart();
                chart.dataProvider = lineChartData;
                chart.pathToImages = "/t/amcharts/images/";
                chart.categoryField = "date";

                // sometimes we need to set margins manually
                // autoMargins should be set to false in order chart to use custom margin values
                chart.autoMargins = false;
                chart.marginRight = 0;
                chart.marginLeft = 0;
                chart.marginBottom = 0;
                chart.marginTop = 0;

                // AXES
                // category                
                var categoryAxis = chart.categoryAxis;
                categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
                categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD
                categoryAxis.inside = true;
                categoryAxis.gridAlpha = 0;
                categoryAxis.tickLength = 0;
                categoryAxis.axisAlpha = 0;
                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.dashLength = 4;
                valueAxis.axisAlpha = 0;
                chart.addValueAxis(valueAxis);

                // GRAPH
                 var graph = new AmCharts.AmGraph();
                graph.dashLength = 3;
                graph.lineColor = "#88838e";
                graph.valueField = "value";
                graph.dashLength = 3;
                graph.bullet = "round";
                chart.addGraph(graph);

                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.cursorAlpha = 0;
                chart.addChartCursor(chartCursor);

                // WRITE
                chart.write("chartdiv");
            });
        </script>
    </head>
    
    <body>
        <div id="chartdiv" style="width:100%; height:300px;"></div>
    </body>

</html>	
	
	
	
HTML;
$TMPL['user_cp_content'] .= '<br/><hr/>';
$TMPL['user_cp_content'] .= '<h3>Просмотры страницы сервера за текущий месяц</h3>';
$TMPL['user_cp_content'] .= '<hr/>';
$TMPL['user_cp_content'] .= $head;
$TMPL['user_cp_content'] .= $data;
$TMPL['user_cp_content'] .= $foot;
  }
 function graphics_clicks() {
    global $CONF, $DB, $LNG, $TMPL;


	$today = date('j');
	$days_in_month = date('t');	
	$username = $TMPL['username'];
	$query = "SELECT  *  FROM  rtt_graphics_data WHERE `username` =  '$username'";  
	//echo $query;
	$result = mysql_query($query)  or  die(mysql_error()); 

	$value = array();
	while($info = mysql_fetch_array($result))  
	{
		for($i=1; $i<=$today; $i++)
		{
			$value[$i] = $info["day_{$i}_clicks"];
		}
	}	
	

	$month = mysql_fetch_row(mysql_query("SELECT last_new_month from rtt_etc"));
	$month = $month[0];
	$month = $month - 1;
	$head = <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>amCharts examples</title>
        <link rel="stylesheet" href="style.css" type="text/css">
        <script src="/t/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="/t/amcharts/raphael.js" type="text/javascript"></script>
HTML;

		for ( $i=1; $i<=$today; $i++) {
			$form = <<<HTML
			{date: new Date(2012, {$month}, {$i}),value: {$value[$i]}}
HTML;
		$help .= $form;
		if ($i<$today) {
		$help .= ',';
			}
		}
		//echo $help;
$data = <<<HTML
       <script type="text/javascript">
            var lineChartData = [{$help}];
HTML;

$foot = <<<HTML
            AmCharts.ready(function () {
                var chart = new AmCharts.AmSerialChart();
                chart.dataProvider = lineChartData;
                chart.pathToImages = "/t/amcharts/images/";
                chart.categoryField = "date";

                // sometimes we need to set margins manually
                // autoMargins should be set to false in order chart to use custom margin values
                chart.autoMargins = false;
                chart.marginRight = 0;
                chart.marginLeft = 0;
                chart.marginBottom = 0;
                chart.marginTop = 0;

                // AXES
                // category                
                var categoryAxis = chart.categoryAxis;
                categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
                categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD
                categoryAxis.inside = true;
                categoryAxis.gridAlpha = 0;
                categoryAxis.tickLength = 0;
                categoryAxis.axisAlpha = 0;
                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.dashLength = 4;
                valueAxis.axisAlpha = 0;
                chart.addValueAxis(valueAxis);

                // GRAPH
                 var graph = new AmCharts.AmGraph();
                graph.dashLength = 3;
                graph.lineColor = "#88838e";
                graph.valueField = "value";
                graph.dashLength = 3;
                graph.bullet = "round";
                chart.addGraph(graph);

                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.cursorAlpha = 0;
                chart.addChartCursor(chartCursor);

                // WRITE
                chart.write("chartdiv");
            });
        </script>
    </head>
    
    <body>
        <div id="chartdiv" style="width:100%; height:300px;"></div>
    </body>

</html>	
	
	
	
HTML;
$TMPL['user_cp_content'] .= '<br/><hr/>';
$TMPL['user_cp_content'] .= '<h3>Переходы на сайт сервера за текущий месяц</h3>';
$TMPL['user_cp_content'] .= '<hr/>';
$TMPL['user_cp_content'] .= $head;
$TMPL['user_cp_content'] .= $data;
$TMPL['user_cp_content'] .= $foot;
  }

}
?>