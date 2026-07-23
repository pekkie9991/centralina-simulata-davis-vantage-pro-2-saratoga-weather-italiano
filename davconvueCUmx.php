<?php
$davconvuever = "davconvueCUmx.php - silveracorn.nz MXUI website - Version MX_2.1.3 - 16-Sep-2020";
// MX version 3089
###############################################################################
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA	 02111-1307, USA
################################################################################
# This document uses spaces not tabs
################################################################################
if (file_exists('./Settings.php')) {
    require_once('./Settings.php');
} elseif (file_exists('./settings.php')) {
    require_once('./settings.php');
}
require_once('./weatherlink_sync.php');
$page_id = "davconvue";
################################################################################
#	BEGIN SETTINGS 
################################################################################
if(!isset($tz)) { $tz = 'Europe/Rome'; date_default_timezone_set($tz); }
      // synchronise page refresh
      $period = $interval*60;  // seconds between tagfile uploads
      if(file_exists('./CUtags.php')) {
        $age = time() - filemtime('./CUtags.php');
        if($age < $period) {$metrefresh = $period - $age + 20;} else {$metrefresh = $period/2;}
      } else {
        $metrefresh = 300; // default 5 minutes
      }

// Output the complete HTML page head
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Console Davis Vue - Meteo Limbiate</title>
<meta name="description" content="Console meteo Davis Vantage Vue in tempo reale - Limbiate Villaggio del Sole">
<meta http-equiv="refresh" content="<?php echo $metrefresh; ?>" />
<!-- Bootstrap CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Davis Console CSS -->
<link href="./davcon/davconCU.css" rel="stylesheet"/>
<style>
body { background: #1a1a2e; }
.davcon-page-wrapper { background: #16213e; min-height: 100vh; }
.davcon-header {
    background: linear-gradient(135deg, #0f3460, #16213e);
    padding: 12px 24px;
    border-bottom: 2px solid #3d8eb9;
    margin-bottom: 18px;
    display: flex; align-items: center; justify-content: space-between;
}
.davcon-header h1 { font-size: 1.3rem; color: #fff; margin: 0; font-weight: 600; }
.davcon-header a { color: #aaa; text-decoration: none; font-size: 0.88rem; }
.davcon-header a:hover { color: #3d8eb9; }
.davcon-footer { text-align:center; padding: 14px; color: #666; font-size: 0.78rem; border-top: 1px solid #2a2a4a; margin-top: 24px; }
</style>
<!-- Davis Console JS -->
<script src="./davcon/jquery.flot-081.min.js"></script>
<script src="./davcon/jscroller2-1.61.min.js"></script>
<script src="./davcon/jquery.davconsoleCU.min.js"></script>
</head>
<body>
<div class="davcon-page-wrapper">
<div class="davcon-header">
  <h1>&#127968; Console Davis Vantage Vue</h1>
  <a href="./wxindex.php">&#8592; Torna al Sito</a>
</div>

<!-- <?php echo $davconvuever ?> -->
<?php
require_once("./nav_menu.php");
// These settings used in this script and/or in ./davcon/jquery.davconsoleCU.js
$graphurl	= './davcon24.txt'; // for Cumulus graph data
		if (!file_exists($graphurl)) {echo '<br /> &nbsp;' . $graphurl . ' not found! <br />' ; return;}
$dataurl		= './realtime-x.txt';	  // for Cumulus 'realtime' data
		if (!file_exists($dataurl)) {echo '<br /> &nbsp;' . $dataurl . ' not found! <br />' ; return;}
		if (file_exists('./sensorlost.php')) {include_once('sensorlost.php');} else {$sensorlost = 0;}
$imgdir		= './davcon/'; // path to jquery, css and images with trailing /
$storm		=	'Y';	// Y or N Only Davis has storm rain
$showsolar	=	'N';	// DO NOT CHANGE !!
$showuv		=	'N';	// DO NOT CHANGE !!
$stnnumbrs	= '1&nbsp;' . ' &nbsp;' . ' &nbsp;' . ' &nbsp;' . ' &nbsp;' . ' &nbsp;';
$showstnnum = true;	// $showstnnum = true; OR $showstnnum = false;
$showantenna= true;	// $showantenna = true; OR $showantenna = false;
// Settings for timing of 'live' image data rotate and compass pointer refresh behaviour
$itimeout	  = 2;	// time between image updates in seconds
$windrotate	  = 2;	// 1 = Wind speed only in wind rose, 2 = wind speed -> wind direction
$dewrotate	  = 7;	
$timeformat	  = 1;	// 1 = Time display in 'metric' format hh:mm
$dateformat	  = 1;	// 1 = Date display in 'metric' format dd/mm
$showupdate	  = 2;	
$showage		= 2;		
$zambretti	= 0;		// 0 = Use Station forecast

$fcastbtn		= './gauges.php';			// forecast button
	$WxCenbtn	= $fcastbtn;				// WXcen button - for Vue
$fcastbtntxt	=	"<span lang='it'>Strumenti</span>";		// Strumenti/Gauges in Italiano
$WxCenbtntxt	=	$fcastbtntxt;			

$graphbtn		=	'./charts.php';		// graph button
$graphbtntxt	=	"<span lang='it'>Grafici</span>";		

$hilowbtn		=	'./records.php';		// hilow button
$hilowbtntxt	=	"<span lang='it'>Record Storici e Mensili</span>";	

$alarmbtn		=	'./todayyest.php';	// alarm button
	$timebtn		=	$alarmbtn;				

$alarmbtntxt	=	"<span lang='it'>Dati Oggi/Ieri</span>";	
	$timebtntxt =	$alarmbtntxt;		

$donebtn		  = './now.php';				// done button
$donebtntxt	  = "<span lang='it'>Ora</span>";	

$taboption	  = 4;							
$showtooltip  = true;						
############################################################################
#	END Settings
############################################################################
// Get time of next update for update message
$timeofnextupdate = get_timeofnextupdate($timehhmmss,$interval);

// Forecast Icon selection (uses original English strings for safety/regex mapping)
if ($zambretti === 1 || $wsforecast === '0') {
	$forecast = $cumulusforecast;
	$fcsticon = get_fcsticonz($forecast,$date,$timehhmmss);	
} else {
	$forecast = $wsforecast;
	$fcsticon = get_fcsticon($forecast,$date,$timehhmmss);	
}

// ORA Traduciamo la previsione in Italiano per la visualizzazione nello scroller e sulla console
$forecast = translate_davis_forecast($forecast);

// Moon Icon
$moonic = get_moon($MoonAge,$ns);

// Load include file for console type - all console type specific code
require_once('./davconvueCUmx-inc.php');
?>
<script>
var wxsoftware	 = 'CU';
var dataurl		 = '<?php echo $dataurl; ?>'
var realint		 = '<?php echo $realint; ?>'
var imgdir		 = '<?php echo $imgdir; ?>'
var showsolar	 = '<?php echo $showsolar; ?>'
var showuv		 = '<?php echo $showuv; ?>'
var itimeout	 = '<?php echo $itimeout; ?>'
var windrotate	 = '<?php echo $windrotate; ?>'
var dewrotate	 = '<?php echo $dewrotate; ?>'
var vpstormrain = '<?php echo $vpstormrain; ?>'
var storm		 = '<?php echo $storm; ?>'
var timeformat	 = '<?php echo $timeformat; ?>'
var dateformat	 = '<?php echo $dateformat; ?>'
var fcastbtn	 = '<?php echo $fcastbtn; ?>'
var WxCenbtn	 = '<?php echo $WxCenbtn; ?>'
var graphbtn	 = '<?php echo $graphbtn; ?>'
var hilowbtn	 = '<?php echo $hilowbtn; ?>'
var alarmbtn	 = '<?php echo $alarmbtn; ?>'
var timebtn		 = '<?php echo $timebtn; ?>'
var donebtn		 = '<?php echo $donebtn; ?>'
var fcsticon	 = '<?php echo $fcsticon; ?>'
var moonic		 = '<?php echo $moonic; ?>'
var sensorlost	 = '<?php echo $sensorlost; ?>'

// GRAPHS
	var d1 = <?php echo $temp ?>;
	var d2 = <?php echo $hum  ?>;
	var d3 = <?php echo $wind ?>;
	var d4 = <?php echo $rain ?>;
	var d5 = <?php echo $baro ?>;
	var d6 = <?php echo $solr ?>;

	var options = {
		xaxis: {mode:null},
		yaxis: {mode:null},
		grid:	 {show:false},
		legend:{ show:false}
	};

	var data1 = {
		data: d1, 
		points: { show: true, fill: true,fillColor: "#053D6C",radius: 1 },
		color: "#053D6C",
		shadowSize: 1
	};

	var data2 = {
		data: d2, 
		points: { show: true, fill: true,fillColor: "#053D6C",radius: 1 },
		color: "#053D6C",
		shadowSize: 1
	};

	var data3 = {
		data: d3, 
		points: { show: true, fill: true,fillColor: "#053D6C",radius: 1 },
		color: "#053D6C",
		shadowSize: 1
	};

	var data4 = {
		data: d4, 
		points: { show: true, fill: true,fillColor: "#053D6C",radius: 1 },
		color: "#053D6C",
		shadowSize: 1
	};

	var data5 = {
		data: d5, 
		points: { show: true, fill: true,fillColor: "#053D6C",radius: 1 },
		color: "#053D6C",
		shadowSize: 1
	};

	var data6 = {
		data: d6, 
		points: { show: true, fill: true,fillColor: "#053D6C",radius: 1 },
		color: "#053D6C",
		shadowSize: 1
	};

	var plot = $.plot($("#placeholder"), [data1], options);
	$("#tempbtn").click(function()  { var plot = $.plot($("#placeholder"), [data1],	options);
		hideGrLab();
		$("#grlab1").css('display', 'inline-block'); // (TEMP)
		$("#grmax").one('click').html('<?php echo $tmax;  ?>');
		$("#grmin").one('click').html('<?php echo $tmin;  ?>');
	});

	$("#humbtn" ).click(function()  { var plot = $.plot($("#placeholder"), [data2], options);
		hideGrLab();
		$("#grlab2").css('display', 'inline-block'); // (HUM)
		$("#grmax").one('click').html('<?php echo $hmax;  ?>');
		$("#grmin").one('click').html('<?php echo $hmin;  ?>');
	});

	$("#windbtn").click(function()  { var plot = $.plot($("#placeholder"), [data3], options);
		hideGrLab();
		$("#grlab3").css('display', 'inline-block'); // (WIND)
		$("#grmax").one('click').html('<?php echo $wmax;  ?>');
		$("#grmin").one('click').html('<?php echo $wmin;  ?>');
	});

	$("#rainbtn").click(function()  { var plot = $.plot($("#placeholder"), [data4], options);
		hideGrLab();
		$("#grlab4").css('display', 'inline-block'); // (RAIN)
		$("#grmax").one('click').html('<?php echo $rmax;  ?>');
		$("#grmin").one('click').html('<?php echo $rmin;  ?>');
	 });

	$("#barbtn" ).click(function()  { var plot = $.plot($("#placeholder"), [data5], options);
		hideGrLab();
		$("#grlab5").css('display', 'inline-block'); // (BAR)
		$("#grmax").one('click').html('<?php echo $bmax;  ?>');
		$("#grmin").one('click').html('<?php echo $bmin;  ?>');
	});

	$("#solarbtn").click(function()	{ var plot = $.plot($("#placeholder"), [data6], options);
		hideGrLab();
		$("#grlab6").css('display', 'inline-block'); // (SOL)
		$("#grmax").one('click').html('<?php echo $smax;  ?>');
		$("#grmin").one('click').html('<?php echo $smin;  ?>');
	 });

	$("#tempbtnt").click(function() { var plot = $.plot($("#placeholder"), [data1],	options);
		hideGrLab();
		$("#grlab1").css("display", "inline-block");
		$("#grmax").one('click').html('<?php echo $tmax;  ?>');
		$("#grmin").one('click').html('<?php echo $tmin;  ?>');
	});

	$("#humbtnt" ).click(function() { var plot = $.plot($("#placeholder"), [data2], options);
		hideGrLab();
		$("#grlab2").css("display", "inline-block");
		$("#grmax").one('click').html('<?php echo $hmax;  ?>');
		$("#grmin").one('click').html('<?php echo $hmin;  ?>');
	});

	$("#windbtnt").click(function() { var plot = $.plot($("#placeholder"), [data3], options);
		hideGrLab();
		$("#grlab3").css("display", "inline-block");
		$("#grmax").one('click').html('<?php echo $wmax;  ?>');
		$("#grmin").one('click').html('<?php echo $wmin;  ?>');
	});

	$("#rainbtnt").click(function() { var plot = $.plot($("#placeholder"), [data4], options);
		hideGrLab();
		$("#grlab4").css("display", "inline-block");
		$("#grmax").one('click').html('<?php echo $rmax;  ?>');
		$("#grmin").one('click').html('<?php echo $rmin;  ?>');
	});

	$("#barbtnt" ).click(function() { var plot = $.plot($("#placeholder"), [data5], options);
		hideGrLab();
		$("#grlab5").css("display", "inline-block");
		$("#grmax").one('click').html('<?php echo $bmax;  ?>');
		$("#grmin").one('click').html('<?php echo $bmin;  ?>');
	});

	$("#solarbtnt").click(function() { var plot = $.plot($("#placeholder"), [data6], options);
		hideGrLab();
		$("#grlab6").css("display", "inline-block");
		$("#grmax").one('click').html('<?php echo $smax;  ?>');
		$("#grmin").one('click').html('<?php echo $smin;  ?>');
	 });

	$('a[rel*="external"]').click( function() {
		window.open(this.href);
		return false;
	});

function hideGrLab() {
	  $("#grlab1").css('display', 'none'); // TEMP
	  $("#grlab2").css('display', 'none'); // HUM
	  $("#grlab3").css('display', 'none'); // WIND
	  $("#grlab4").css('display', 'none'); // RAIN
	  $("#grlab5").css('display', 'none'); // BAR
	  $("#grlab6").css('display', 'none'); // SOL
}
</script>

<?php
// For time of next update message
$tag_time=$timehhmmss;
function get_timeofnextupdate($tag_time,$interval) {
$SS = substr($tag_time,6,2);
$MM = substr($tag_time,3,2) + $interval;
$HH = substr($tag_time,0,2);
if ($MM >= 60) {$MM = $MM - 60; $HH = $HH + 1;}
if ($HH >= 24) {$HH = 0;}
$nxtupd = $HH . ':' . $MM . ':' . $SS;
$timeofnextupdate = date('H:i',strtotime($nxtupd));
return $timeofnextupdate;
}

// Forecast Icon DAVIS
function get_fcsticon($forecast,$date,$tag_time) {
$DC_fcsttmp = "DC_{$forecast}";	
if (
	preg_match('/Mostly clear/i',									$DC_fcsttmp) )		// mclr.png
		{$fcsticon = "mclr.png";} else if (
	preg_match('/increasing clouds and warmer/i',			$DC_fcsttmp) ||	// pcld.png
	preg_match('/warmer. Precipitation possible within 24/i', $DC_fcsttmp) ||	// pcld.png
	preg_match('/Increasing clouds with/i',					$DC_fcsttmp) ||	// pcld.png
	preg_match('/Partly cloudy/i',								$DC_fcsttmp) )		// pcld.png
		{$fcsticon = "pcld.png";} else if (
	preg_match('/cooler. precipitation possible within 12/i', $DC_fcsttmp) ||	// rain.png
	preg_match('/cooler. Precipitation likely. Windy/i', $DC_fcsttmp) )		// rain.png
		{$fcsticon = "rain.png";} else if (
	preg_match('/Precipitation ending within 6/i',			$DC_fcsttmp) ||	// mcld.png
	preg_match('/clearing, cooler and windy/i',				$DC_fcsttmp) ||	// mcld.png
	preg_match('/mostly cloudy and cooler/i',					$DC_fcsttmp) ||	// mcld.png
	preg_match('/Mostly cloudy with/i',							$DC_fcsttmp) ||	// mcld.png
	preg_match('/change. possible wind shift/i',				$DC_fcsttmp) ||	// mcld.png
	preg_match('/likely/i',											$DC_fcsttmp) ||	// mcld.png
	preg_match('/change. precipitation possible within 24/i', $DC_fcsttmp) ||	// mcld.png
	preg_match('/Precipitation likely possibly/i',			$DC_fcsttmp) ||	// mcld.png
	preg_match('/Precipitation possible within 24/i',		$DC_fcsttmp) ||	// mcld.png
	preg_match('/Precipitation possible within 48/i',		$DC_fcsttmp) ||	// mcld.png
	preg_match('/Unsettled/i',										$DC_fcsttmp) )		// mcld.png
		{$fcsticon = "mcld.png";} else if (
	preg_match('/precipitation continuing/i',					$DC_fcsttmp) ||	// rain.png
	preg_match('/windy within 6/i',								$DC_fcsttmp) ||	// rain.png
	preg_match('/possible within 12/i',							$DC_fcsttmp) ||	// rain.png
	preg_match('/possible within 6/i',							$DC_fcsttmp) ||	// rain.png
	preg_match('/ending in/i',										$DC_fcsttmp) ||	// rain.png
	preg_match('/ending within 12/i',							$DC_fcsttmp) )		// rain.png
		{$fcsticon = "rain.png";} else if (
	preg_match('/Partialy cloudy, Rain possible/i',			$DC_fcsttmp) )		// pcldrain.png
		{$fcsticon = "pcldrain.png";} else if (
	preg_match('/Mostly cloudy, Rain possible/i',			$DC_fcsttmp) )		// mcldrain.png
		{$fcsticon = "mcldrain.png";} else if (
	preg_match('/Partialy cloudy, Snow/i',						$DC_fcsttmp) )		// pcldsnow.png
		{$fcsticon = "pcldsnow.png";} else if (
	preg_match('/Mostly cloudy, Snow/i',						$DC_fcsttmp) )		// pcldsnow.png
		{$fcsticon = "mcldsnow.png";} else if (
	preg_match('/Rain and/i',										$DC_fcsttmp) )		// rainsnow.png
		{$fcsticon = "rainsnow.png";} else if (
	preg_match('/Clear/i',											$DC_fcsttmp) ||	// mclr.png
	preg_match('/Sunny/i',											$DC_fcsttmp) )		// mclr.png
		{$fcsticon = "mclr.png";} else if (
	preg_match('/Cloudy/i',											$DC_fcsttmp) )		// mcld.png
		{$fcsticon = "mcld.png";} else if (
	preg_match('/Rain/i',											$DC_fcsttmp) )		// rain.png
		{$fcsticon = "rain.png";} else if (
	preg_match('/Snow/i',											$DC_fcsttmp) )		// snow.png
		{$fcsticon = "snow.png";} else if (
	preg_match("/(FORECAST)/",										$DC_fcsttmp) )		
		{$fcsticon = "grid.png";} else {						
	$not_found = substr($DC_fcsttmp,3) . "\n";
	file_put_contents( "./fcst_not_found.txt" , $date . " " . $tag_time . ":- Station Forecast was: " . $not_found , FILE_APPEND);
	$fcsticon = "grid.png";
	}
return $fcsticon;
}

// Forecast Icon ZAMBRETTI
function get_fcsticonz($forecast) {
$DC_fcsttmp = "DC_{$forecast}";	
if (
	preg_match("/(Settled fine)/i",									$DC_fcsttmp) ||	// mclr.png
	preg_match("/(Fine weather)/i",									$DC_fcsttmp) )		// mclr.png
		{$fcsticon = "mclr.png";} else if (
	preg_match("/(Becoming fine)/i",									$DC_fcsttmp) ||	// pcld.png
	preg_match("/(Fine, becoming less settled)/i",				$DC_fcsttmp) ||	// pcld.png
	preg_match("/(Fine, possible showers)/i",						$DC_fcsttmp) )		// pcld.png
		{$fcsticon = "pcld.png";} else if (
	preg_match("/(Fairly fine, improving)/i",						$DC_fcsttmp) ||	// mcld.png
	preg_match("/(Fairly fine, possible showers early)/i",	$DC_fcsttmp) ||	// mcld.png
	preg_match("/(Fairly fine, showery later)/i",				$DC_fcsttmp) )		// mcld.png
		{$fcsticon = "mcld.png";} else if (
	preg_match("/(Showery early, improving)/i",					$DC_fcsttmp) ||	// pcldrain.png
	preg_match("/(Changeable, mending)/i",							$DC_fcsttmp) ||	// pcldrain.png
	preg_match("/(Fairly fine, showers likely)/i",				$DC_fcsttmp) ||	// pcldrain.png
	preg_match("/(Rather unsettled clearing later)/i",			$DC_fcsttmp) ||	// pcldrain.png
	preg_match("/(Unsettled, probably improving)/i",			$DC_fcsttmp) ||	// pcldrain.png
	preg_match("/(Showery, bright intervals)/i",					$DC_fcsttmp) ||	// pcldrain.png
	preg_match("/(Showery, becoming less settled)/i",			$DC_fcsttmp) ||	// pcldrain.png
	preg_match("/(Changeable, some precipitation)/i",			$DC_fcsttmp) ||	// pcldrain.png
	preg_match("/(Unsettled, short fine intervals)/i",			$DC_fcsttmp) ||	// pcldrain.png
	preg_match("/(Unsettled, precipitation later)/i",			$DC_fcsttmp) ||	// pcldrain.png
	preg_match("/(Unsettled, some precipitation)/i",			$DC_fcsttmp) ||	// pcldrain.png
	preg_match("/(Mostly very unsettled)/i",						$DC_fcsttmp) ||	// mcldrain.png
	preg_match("/(Occasional precipitation, worsening)/i",	$DC_fcsttmp) )		// mcldrain.png
		{$fcsticon = "pcldrain.png";} else if (
	preg_match("/(Precipitation at times, very unsettled)/i", $DC_fcsttmp) ||	// rain.png
	preg_match("/(Precipitation at frequent intervals)/i",	$DC_fcsttmp) ||	// rain.png
	preg_match("/(Precipitation, very unsettled)/i",			$DC_fcsttmp) ||	// rain.png
	preg_match("/(Stormy, may improve)/i",							$DC_fcsttmp) ||	// rain.png
	preg_match("/(Stormy, much precipitation)/i",				$DC_fcsttmp) ||	// rain.png
	preg_match("/(Exceptional Weather)/i",							$DC_fcsttmp) )		// rain.png
		{$fcsticon = "rain.png";}	else {
		$fcsticon = "grid.png";}																
return $fcsticon;
}

// Moon icon selection
function get_moon($MoonAge,$ns) {
 if($MoonAge <= 1	  || $MoonAge >= 27.7) { $moonic = $ns . "moonnew.png";	} else
 if($MoonAge > 1	  && $MoonAge <= 6.5)  { $moonic = $ns . "moonwaxc.png"; } else
 if($MoonAge > 6.5  && $MoonAge <= 7.5)  { $moonic = $ns . "moonfqtr.png"; } else
 if($MoonAge > 7.5  && $MoonAge <= 13.5) { $moonic = $ns . "moonwaxg.png"; } else
 if($MoonAge > 13.5 && $MoonAge <= 14.5) { $moonic = $ns . "moonfull.png"; } else
 if($MoonAge > 14.5 && $MoonAge <= 20.5) { $moonic = $ns . "moonwang.png"; } else
 if($MoonAge > 20.5 && $MoonAge <= 21.5) { $moonic = $ns . "moonlqtr.png"; } else
 if($MoonAge > 21.5 && $MoonAge <  27.7) { $moonic = $ns . "moonwanc.png"; }
return strtolower($moonic);
}

	require_once('./footer.php');
?>
<div class="davcon-footer">
    Stazione Meteo Limbiate &mdash; Villaggio del Sole &mdash; Davis Vantage Vue<br>
    Dati in tempo reale da WeatherLink &bull; Aggiornamento automatico ogni 5 minuti
</div>
</div><!-- .davcon-page-wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

### 6. File: `davconvueCUmx-inc.php`
Questo file gestisce l'interfaccia grafica e i layer testuali della console Vantage Vue tradotta integralmente in lingua italiana.

```php
<!-- davconvueCUmx-inc.php - silveracorn.nz MXUI website - Version MX_2.1.3 - 16-Sep-2020 -->
<?php
// MX version 3089
// BEGIN Cumulus Graph Data
if (file_exists($graphurl)) {
	for($i=0;$i<26;$i++){	// Vue has 26 data points
		$tmcb[] = (time()+($i*3600)*1000); // $tmcb == TiMe CB
	}
} else {echo "&nbsp;&nbsp;$graphurl NOT Found!"; Return;} // for Cumulus graph data
$graphurl = implode(' ', file($graphurl));
$c = explode(' ', $graphurl);
// $temp 26 datapoints for last 24 Hr temp graph 1/hr plus one each end
$temp = '[['.$tmcb[0].','.$c[0].'],['.$tmcb[1].','.$c[1].'],['.$tmcb[2].','.$c[2].'],['.$tmcb[3].','.$c[3].'],['.$tmcb[4].','.$c[4].'],['.$tmcb[5].','.$c[5].'],['.$tmcb[6].','.$c[6].'],['.$tmcb[7].','.$c[7].'],['.$tmcb[8].','.$c[8].'],['.$tmcb[9].','.$c[9].'],['.$tmcb[10].','.$c[10].'],['.$tmcb[11].','.$c[11].'],['.$tmcb[12].','.$c[12].'],['.$tmcb[13].','.$c[13].'],['.$tmcb[14].','.$c[14].'],['.$tmcb[15].','.$c[15].'],['.$tmcb[16].','.$c[16].'],['.$tmcb[17].','.$c[17].'],['.$tmcb[18].','.$c[18].'],['.$tmcb[19].','.$c[19].'],['.$tmcb[20].','.$c[20].'],['.$tmcb[21].','.$c[21].'],['.$tmcb[22].','.$c[22].'],['.$tmcb[23].','.$c[23].'],['.$tmcb[24].','.$c[24].'],['.$tmcb[25].','.$c[25].']]';
// $hum 26 datapoints for last 24 Hr humidity graph 1/hr plus one each end
$hum = '[['.$tmcb[0].','.$c[26].'],['.$tmcb[1].','.$c[27].'],['.$tmcb[2].','.$c[28].'],['.$tmcb[3].','.$c[29].'],['.$tmcb[4].','.$c[30].'],['.$tmcb[5].','.$c[31].'],['.$tmcb[6].','.$c[32].'],['.$tmcb[7].','.$c[33].'],['.$tmcb[8].','.$c[34].'],['.$tmcb[9].','.$c[35].'],['.$tmcb[10].','.$c[36].'],['.$tmcb[11].','.$c[37].'],['.$tmcb[12].','.$c[38].'],['.$tmcb[13].','.$c[39].'],['.$tmcb[14].','.$c[40].'],['.$tmcb[15].','.$c[41].'],['.$tmcb[16].','.$c[42].'],['.$tmcb[17].','.$c[43].'],['.$tmcb[18].','.$c[44].'],['.$tmcb[19].','.$c[45].'],['.$tmcb[20].','.$c[46].'],['.$tmcb[21].','.$c[47].'],['.$tmcb[22].','.$c[48].'],['.$tmcb[23].','.$c[49].'],['.$tmcb[24].','.$c[50].'],['.$tmcb[25].','.$c[51].']]';
// $wind 26 datapoints for last 24 Hr windspeed graph 1/hr plus one each end
$wind = '[['.$tmcb[0].','.$c[52].'],['.$tmcb[1].','.$c[53].'],['.$tmcb[2].','.$c[54].'],['.$tmcb[3].','.$c[55].'],['.$tmcb[4].','.$c[56].'],['.$tmcb[5].','.$c[57].'],['.$tmcb[6].','.$c[58].'],['.$tmcb[7].','.$c[59].'],['.$tmcb[8].','.$c[60].'],['.$tmcb[9].','.$c[61].'],['.$tmcb[10].','.$c[62].'],['.$tmcb[11].','.$c[63].'],['.$tmcb[12].','.$c[64].'],['.$tmcb[13].','.$c[65].'],['.$tmcb[14].','.$c[66].'],['.$tmcb[15].','.$c[67].'],['.$tmcb[16].','.$c[68].'],['.$tmcb[17].','.$c[69].'],['.$tmcb[18].','.$c[70].'],['.$tmcb[19].','.$c[71].'],['.$tmcb[20].','.$c[72].'],['.$tmcb[21].','.$c[73].'],['.$tmcb[22].','.$c[74].'],['.$tmcb[23].','.$c[75].'],['.$tmcb[24].','.$c[76].'],['.$tmcb[25].','.$c[77].']]';
// $rain 26 datapoints for last 24 Hr rain graph 1/hr plus one each end
$rain = '[['.$tmcb[0].','.$c[78].'],['.$tmcb[1].','.$c[79].'],['.$tmcb[2].','.$c[80].'],['.$tmcb[3].','.$c[81].'],['.$tmcb[4].','.$c[82].'],['.$tmcb[5].','.$c[83].'],['.$tmcb[6].','.$c[84].'],['.$tmcb[7].','.$c[85].'],['.$tmcb[8].','.$c[86].'],['.$tmcb[9].','.$c[87].'],['.$tmcb[10].','.$c[88].'],['.$tmcb[11].','.$c[89].'],['.$tmcb[12].','.$c[90].'],['.$tmcb[13].','.$c[91].'],['.$tmcb[14].','.$c[92].'],['.$tmcb[15].','.$c[93].'],['.$tmcb[16].','.$c[94].'],['.$tmcb[17].','.$c[95].'],['.$tmcb[18].','.$c[96].'],['.$tmcb[19].','.$c[97].'],['.$tmcb[20].','.$c[98].'],['.$tmcb[21].','.$c[99].'],['.$tmcb[22].','.$c[100].'],['.$tmcb[23].','.$c[101].'],['.$tmcb[24].','.$c[102].'],['.$tmcb[25].','.$c[103].']]';
// $baro 26 datapoints for last 24 Hr baro graph 1/hr plus one each end
$baro = '[['.$tmcb[0].','.$c[104].'],['.$tmcb[1].','.$c[105].'],['.$tmcb[2].','.$c[106].'],['.$tmcb[3].','.$c[107].'],['.$tmcb[4].','.$c[108].'],['.$tmcb[5].','.$c[109].'],['.$tmcb[6].','.$c[110].'],['.$tmcb[7].','.$c[111].'],['.$tmcb[8].','.$c[112].'],['.$tmcb[9].','.$c[113].'],['.$tmcb[10].','.$c[114].'],['.$tmcb[11].','.$c[115].'],['.$tmcb[12].','.$c[116].'],['.$tmcb[13].','.$c[117].'],['.$tmcb[14].','.$c[118].'],['.$tmcb[15].','.$c[119].'],['.$tmcb[16].','.$c[120].'],['.$tmcb[17].','.$c[121].'],['.$tmcb[18].','.$c[122].'],['.$tmcb[19].','.$c[123].'],['.$tmcb[20].','.$c[124].'],['.$tmcb[21].','.$c[125].'],['.$tmcb[22].','.$c[126].'],['.$tmcb[23].','.$c[127].'],['.$tmcb[24].','.$c[128].'],['.$tmcb[25].','.$c[129].']]';
// solr not used in Vue but required for common php code
$solr = '[['.$tmcb[0].','.$c[131].'],['.$tmcb[1].','.$c[132].'],['.$tmcb[2].','.$c[133].'],['.$tmcb[3].','.$c[134].'],['.$tmcb[4].','.$c[135].'],['.$tmcb[5].','.$c[136].'],['.$tmcb[6].','.$c[137].'],['.$tmcb[7].','.$c[138].'],['.$tmcb[8].','.$c[139].'],['.$tmcb[9].','.$c[140].'],['.$tmcb[10].','.$c[141].'],['.$tmcb[11].','.$c[142].'],['.$tmcb[12].','.$c[143].'],['.$tmcb[13].','.$c[144].'],['.$tmcb[14].','.$c[145].'],['.$tmcb[15].','.$c[146].'],['.$tmcb[16].','.$c[147].'],['.$tmcb[17].','.$c[148].'],['.$tmcb[18].','.$c[149].'],['.$tmcb[19].','.$c[150].'],['.$tmcb[20].','.$c[151].'],['.$tmcb[21].','.$c[152].'],['.$tmcb[22].','.$c[153].'],['.$tmcb[23].','.$c[154].'],['.$tmcb[24].','.$c[155].']]';
// extract max & min values for each graph data type to display max & min values as graph scale is not constant but scales to data range
$t = array_slice($c, 0, 26);
	$tmax = max($t);
	$tmin = min($t);
$h = array_slice($c, 26, 26);
	$hmax = max($h);
	$hmin = min($h);
$w = array_slice($c, 52, 26);
	$wmax = max($w);
	$wmin = min($w);
$r = array_slice($c, 78, 26);
	$rmax = round(max($r),1);
	$rmin = round(min($r),1);
$b = array_slice($c, 104, 26);
	$bmax = round(max($b),1);
	$bmin = round(min($b),1);
$s = array_slice($c, 131, 25);  // solar not used in Vue but required for common php code
	$smax = max($s);
	$smin = min($s);
// END CU graph data
$realint = $c[157]*1000; // realtime file update interval in millisecs
$time24 = $c[160];
if ($timeformat === 2) {
	$n_ampm = (substr($timeofnextupdate,0,2) < 12) ? " AM" : " PM";
	$nextupdate = (substr($timeofnextupdate,0,2) > 12) ? (substr($timeofnextupdate,0,2) - 12).":".substr($timeofnextupdate,3,2).$n_ampm : $timeofnextupdate.$n_ampm;
	$t_ampm = (substr($time24,0,2) < 12) ? " AM" : " PM";
	$timedc = (substr($time24,0,2) > 12) ? (substr($time24,0,2) - 12).":".substr($time24,3,2).$t_ampm : $time24.$t_ampm;
} else {
	$nextupdate = $timeofnextupdate; // 24 hr format
	$timedc = $time24;					// 24 hr format
}
$stormmin	= ($rainunit == 'mm')? 0.4 : 0.02;
?>
<script>
var n=0;
function BackLight() {
	n++;
	if (n%2)
		document.getElementById('vueconsole').style.backgroundImage = "url('./davcon/vue_console.png')";
	else
		document.getElementById('vueconsole').style.backgroundImage = "url('./davcon/vue_console_lit.png')";
}
</script>
<div id="content">
	<noscript><div class="warning"><h1><span lang="it">ABILITA JAVASCRIPT PER GLI AGGIORNAMENTI IN TEMPO REALE!</span></h1></div></noscript>
<br />
<div id="main-copy">
<div style="text-align:left; margin:0 auto; width:700px;">
<div class="vueconsole" id="vueconsole">

<!-- TIME DATE -->
<span class="vajax" id="cajaxhhmm" style="font-size: 20px; top: 96px; right: 480px;"></span>
<span class="small" id="cajaxampm" style="top: 106px; right: 464px;"></span>
<span class="vajax" id="cajaxddmo" style="font-size: 20px; top: 96px; right: 410px;"></span>

<!-- MOON ICON -->
<span class="vajax" id="cajaxmoon" style="top: 129px; left: 195px;"></span>

<!-- FORECAST ICON -->
<span class="vajax" id="cajaxicon" style="top: 154px; left: 195px;"></span>

<!-- WIND -->
<div class="vajax" id="wdir" style=" top: 133px; left: 86px; position: relative;">
<div id="windarrow" style= "font-size: 15px; padding: 0px; position: absolute; top: 33px; left: 0px; transform-origin: 47px 50%">&#10148;</div>
</div>
<span class="vajax" id="cajaxwind" style="top: 155px; right: 444px;"></span>
<span class="small" id="cajaxwindu" style="top:152px; left: 130px;"></span>
<span class="small" id="cajaxwinddu" style="top:164px; left: 158px;"></span>

<!-- TEMP INSIDE -->
<span class="vajax"  id="cajaxitemp" style="top: 92px; right: 322px;"></span>
<span class="smallb" style="top:102px; left: 280px;"><?php echo $tempunit; ?></span>
<span class="small"  lang="it" style="top: 126px; left: 240px;">INTERNA</span>

<!-- TEMP OUTSIDE -->
<span class="vajax" id="cajaxtemp" style="top: 92px; right: 238px;"></span>
<span class="smallb" style="top:102px; left: 365px;"><?php echo $tempunit; ?></span>
<span class="small" lang="it" style= "top: 126px; left: 316px;">ESTERNA</span>

<!-- HUMIDITY OUTSIDE -->
<span class="vajax" id="cajaxhumidity" style="top: 129px;  right: 233px;"></span>

<!-- HUMIDITY INSIDE -->
<span class="vajax" id="cajaxihumidity" style="top: 129px;  right: 321px;"></span>

<!-- BAROMETER -->
<span class="vajax" id="cajaxbaroarrow" style="top:164px; left:362px; font-size: 12px; z-index:100;">&#10148;</span>
<span class="vajax" id="cajaxbaro" style="top: 156px; right: 245px;"></span>
<span class="small" style="top:178px; left: 360px;"><?php echo $pressunit; ?></span>

<!-- APPARENT TEMP -->
<span class="small" id="app1" lang="it" style="top:197px; right:353px; display: none;">APPARENTE</span>
<span class="small" id="app2" lang="it" style="top:197px; right:353px; display: none;">RUGIADA</span>
<span class="small" id="app3" lang="it" style="top:197px; right:353px; display: none;">HUMIDEX</span>
<span class="small" id="app4" lang="it" style="top:197px; right:353px; display: none;">WIND CHILL</span>
<span class="small" id="app5" lang="it" style="top:197px; right:353px; display: none;">IND. CALORE</span>
<span class="vajax"	id="cajaxapp" style="top: 200px; right: 353px;"></span>
<span class="smallb" id="itempuom" style="top:210px; left: 250px;"><?php echo $tempunit; ?></span>

<!-- IS RAINING or SNOW ICON -->
<span class="vajax" id="cajaxumbr" style="top:200px; right:314px;"></span>

<!-- DAILY RAIN -->
<span class="small" id="rrd" lang="it" style="top:197px; right:230px;">PIOGGIA GIORN.</span>
<span class="vajax" id="cajaxrain" style="top:200px; right:250px;"></span>
<span class="small" id="rrt" style="top:222px; left:352px;"><?php echo $rainunit; ?></span>

<!-- RAIN RATE -->
<span class="small" id="rrth" style="top:261px; left:352px;"><?php echo $rainunit; ?></span>
<span class="small" id="rrh" lang="it" style= "top:261px; left:372px; display:none;">/ora</span>
<span class="small" id="rrh1" lang="it" style="top:237px; right:230px;">INTENS. PIOGGIA</span>
<span class="small" id="rrh2" lang="it" style="top:237px; right:230px;">PIOGGIA ORA</span>
<span class="small" id="rrh3" lang="it" style="top:237px; right:230px;">PIOGGIA MESE</span>
<span class="small" id="rrh4" lang="it" style="top:237px; right:230px;">PIOGGIA ANNO</span>
<span class="vajax" id="cajaxrainratehr" style="top:240px; right:250px;"></span>

<!-- FORECAST TEXT -->
<div id="vue_scroller_container">
 <div class="jscroller2_left jscroller2_speed-50 jscroller2_mousemove jscroller2_ignoreleave jscroller2_dynamic">
 <span  lang= "it"><?php echo $forecast; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
 <div class="jscroller2_left_endless jscroller2"><span  lang= "it"><?php echo $forecast; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
</div>

<!-- STATION NUMBERS -->
<?php if ($showstnnum) { ?>
	<span class="small" style="position:absolute; top:299px; left:270px;"><span lang="it">STAZIONE N.</span><?php echo $stnnumbrs; ?></span>
<?php } ?>

<!-- ANTENNA ICON -->
<?php if ($showantenna) { ?>
	<span class="vajax" id="cajaxanten" style="top:207px; left:167px;"></span>
<?php } ?>

<!-- GRAPH LABEL -->
<span class="small" id="grlab1" lang="it" style="position:absolute; top:230px; left:80px; display: inline-block;">TEMP</span>
<span class="small" id="grlab2" lang="it" style="position:absolute; top:230px; left:80px; display: none;">UMID</span>
<span class="small" id="grlab3" lang="it" style="position:absolute; top:230px; left:80px; display: none;">VENTO</span>
<span class="small" id="grlab4" lang="it" style="position:absolute; top:230px; left:80px; display: none;">PIOGGIA</span>
<span class="small" id="grlab5" lang="it" style="position:absolute; top:230px; left:80px; display: none;">BAROM</span>
<span class="small" id="grmax" style="position:absolute; top:240px; left:231px;"><?php echo $tmax; ?></span>
<span class="small" id="grmin" style="position:absolute; top:299px; left:231px;"><?php echo $tmin; ?></span>

<!-- GRAPH WINDOW -->
<div id="placeholder" style="height:62px; width:148px; position:absolute; top:246px; left:79px;"></div>

<!-- BUTTONS Console LEFT column IMAGE -->
<div id="lampsbtn" onclick="BackLight()" style="cursor:pointer; height:27px; width:43px; position:absolute; top:410px; left:90px"></div>
<div id="tempbtn"  style="cursor:pointer; height:27px; width:43px; position:absolute; top:410px; left:140px;"></div>
<div id="humbtn"   style="cursor:pointer; height:27px; width:43px; position:absolute; top:410px; left:190px;"></div>
<div id="windbtn"  style="cursor:pointer; height:27px; width:43px; position:absolute; top:410px; left:240px;"></div>
<div id="rainbtn"  style="cursor:pointer; height:27px; width:43px; position:absolute; top:410px; left:290px;"></div>
<div id="barbtn"   style="cursor:pointer; height:27px; width:43px; position:absolute; top:410px; left:340px;"></div>

<!-- BUTTONS Console RIGHT column IMAGE -->
<?php   if($WxCenbtntxt != "") { ?>
   <?php if ($taboption===2||$taboption===3) { ?><div id="WxCenbtn"
      <?php if ($showtooltip) { ?> data-vuc-tip="NewTab" onclick="window.open('<?php echo $WxCenbtn ?>')"
      <?php } else { ?> onclick="window.open('<?php echo $WxCenbtn ?>')" <?php } ?>
   <?php } else { ?><div id="WxCenbtn" onclick="top.location.href=WxCenbtn" <?php } ?>
   style="cursor:pointer; height:27px; width:43px; position:absolute; top:448px; left:140px"></div>
<?php } if($graphbtntxt != "") { ?>
   <?php if ($taboption===2||$taboption===3) { ?><div id="graphbtn"
      <?php if ($showtooltip) { ?> data-vuc-tip="NewTab" onclick="window.open('<?php echo $graphbtn ?>')"
      <?php } else { ?> onclick="window.open('<?php echo $graphbtn ?>')" <?php } ?>
   <?php } else { ?><div id="graphbtn" onclick="top.location.href=graphbtn" <?php } ?>
   style="cursor:pointer; height:27px; width:43px; position:absolute; top:448px; left:190px"></div>
<?php } if($hilowbtntxt != "") { ?>
   <?php if ($taboption===2||$taboption===3) { ?><div id="hilowbtn"
      <?php if ($showtooltip) { ?> data-vuc-tip="NewTab" onclick="window.open('<?php echo $hilowbtn ?>')"
      <?php } else { ?> onclick="window.open('<?php echo $hilowbtn ?>')" <?php } ?>
   <?php } else { ?><div id="hilowbtn" onclick="top.location.href=hilowbtn" <?php } ?>
   style="cursor:pointer; height:27px; width:43px; position:absolute; top:448px; left:240px"></div>
<?php } if($timebtntxt != "") { ?>
   <?php if ($taboption===2||$taboption===3) { ?><div id="timebtn"
      <?php if ($showtooltip) { ?> data-vuc-tip="NewTab" onclick="window.open('<?php echo $timebtn ?>')"
      <?php } else { ?> onclick="window.open('<?php echo $timebtn ?>')" <?php } ?>
   <?php } else { ?><div id="timebtn" onclick="top.location.href=timebtn" <?php } ?>
   style="cursor:pointer; height:27px; width:43px; position:absolute; top:448px; left:290px"></div>
<?php } if($donebtntxt != "") { ?>
   <?php if ($taboption===2||$taboption===3) { ?><div id="donebtn"
      <?php if ($showtooltip) { ?> data-vuc-tip="NewTab" onclick="window.open('<?php echo $donebtn ?>')"
      <?php } else { ?> onclick="window.open('<?php echo $donebtn ?>')" <?php } ?>
   <?php } else { ?><div id="donebtn" onclick="top.location.href=donebtn" <?php } ?>
   style="cursor:pointer; height:27px; width:43px; position:absolute; top:448px; left:340px"></div>
<?php } ?>
</div> <!-- END id="content" -->
<!-- preload main image -->
<div id="preload" style="display:none"><img src="./davcon/vue_console.png" alt=""></div>
<!-- showupdate and show age -->
<div class="content">
	<div class="buttonTable">
		<div class="rowCenter">
		<?php if ($showupdate === 1) { ?>
			<div class="cellLeft50"><span lang="it">Prossimo agg. grafico e previsioni</span> @ ~ <?php echo $nextupdate; ?></div>
		<?php } if ($showupdate === 2) { ?>
			<div class="cellLeft50"><span lang="it">Grafico e previsioni aggiornati</span> @ <?php echo $timedc; ?></div>
		<?php } if ($showage === 1) { ?>
			<div class="cellLeft50">&nbsp;&nbsp;&nbsp;<span lang="it">Prossimo agg. in tempo reale in</span> ~ &nbsp;<b><span id="ajaxcounterdcdn"></span></b>&nbsp;<span lang="it">sec</span></div>
		<?php } if ($showage === 2) { ?>
			<div class="cellLeft50">&nbsp;&nbsp;&nbsp;<span lang="it">Dati in tempo reale aggiornati</span>&nbsp;<b><span id="ajaxcounterdcup"></span></b>&nbsp;<span lang="it">sec fa</span></div>
		<?php } ?>
		</div>
	</div>

<!-- BUTTONS TABLE below image -->
	<div class="buttonTable">
		<div class="rowCenter">
			<div class="cellLeft50"> <b><span lang="it">Clicca sui pulsanti della console o della tabella</span></b> </div>
		</div>
	</div>
<!-- Button BACKLIGHT -->
   <div class="buttonTable">
      <div class="rowCenter">
         <div class="cellLeft10"> <input type="button" class="btnwhi" onclick="BackLight()" style="cursor:pointer" value="LUCE" /> </div>
         <div class="cellLeft40"><span lang="it">Accendi/Spegni Retroilluminazione</span></div>
      </div>
<!-- Button TEMP -->
      <div  class="rowCenter">
         <div class="cellLeft10"><input type="button" class="btnbrn" style="cursor:pointer" value="TEMP" id="tempbtnt" /></div>
         <div class="cellLeft40"><span lang="it">Grafico Temperatura Esterna 24 ore</span></div>
<!-- Button WxCen -->
         <?php if($WxCenbtntxt == "") { ?>
            <div class="cellLeft10"><input type="button" class="btnbrn" value="STRUM." disabled /></div>
         <?php } else if ($taboption===1||$taboption===3) { ?>
            <?php if ($showtooltip) { ?>
               <div class="cellLeft10"> <div data-tip="NewTab"><input type="button" class="btnbrn" value="STRUM." style="cursor:pointer" onclick="window.open('<?php echo $WxCenbtn ?>')" /></div></div>
            <?php } else { ?>
               <div class="cellLeft10"> <input type="button" class="btnbrn" value="STRUM." style="cursor:pointer" onclick="window.open('<?php echo $WxCenbtn ?>')" /></div>
            <?php } ?>
         <?php } else { ?>
            <div class="cellLeft10"> <input type="button" class="btnbrn" value="STRUM." style="cursor:pointer" onclick="parent.location= '<?php echo $WxCenbtn ?>' " /></div>
         <?php } ?>
         <div class="cellLeft40"><?php echo $WxCenbtntxt; ?></div>
      </div>
<!-- Button HUM -->
      <div  class="rowCenter">
         <div class="cellLeft10"><input type="button" class="btnbrn" style="cursor:pointer" value="UMID" id="humbtnt"/></div>
         <div class="cellLeft40"><span lang="it">Grafico Umidità Esterna 24 ore</span></div>
<!-- Button GRAPH -->
         <?php if($graphbtntxt == "") { ?>
            <div class="cellLeft10"><input type="button" class="btnbrn" value="GRAFICI" disabled="disabled" /></div>
         <?php } else if ($taboption===1||$taboption===3) { ?>
            <?php if ($showtooltip) { ?>
               <div class="cellLeft10"> <div data-tip="NewTab"><input type="button" class="btnbrn" value="GRAFICI" style="cursor:pointer" onclick="window.open('<?php echo $graphbtn ?>')" /></div></div>
            <?php } else { ?>
               <div class="cellLeft10"><input type="button" class="btnbrn" value="GRAFICI" style="cursor:pointer" onclick="window.open('<?php echo $graphbtn ?>')" /></div>
            <?php } ?>
         <?php } else { ?>
            <div class="cellLeft10"><input type="button" class="btnbrn" value="GRAFICI" style="cursor:pointer" onclick="parent.location= '<?php echo $graphbtn ?>' " /></div>
         <?php } ?>
         <div class="cellLeft40"><?php echo $graphbtntxt; ?></div>
      </div>
<!-- Button WIND -->
      <div  class="rowCenter">
         <div class="cellLeft10"><input type="button" class="btnbrn" style="cursor:pointer" value="VENTO" id="windbtnt"/></div>
         <div class="cellLeft40"><span lang="it">Grafico Velocità Vento 24 ore</span></div>
<!-- Button HI/LOW -->
         <?php if($hilowbtntxt == "") { ?>
            <div class="cellLeft10"><input type="button" class="btnbrn" value="MAX/MIN" disabled="disabled" /></div>
         <?php } else if ($taboption===1||$taboption===3) { ?>
            <?php if ($showtooltip) { ?>
               <div class="cellLeft10"> <div data-tip="NewTab"><input type="button" class="btnbrn" value="MAX/MIN" style="cursor:pointer" onclick="window.open('<?php echo $hilowbtn ?>')" /></div></div>
            <?php } else { ?>
               <div class="cellLeft10"><input type="button" class="btnbrn" value="MAX/MIN" style="cursor:pointer" onclick="window.open('<?php echo $hilowbtn ?>')" /></div>
            <?php } ?>
         <?php } else { ?>
            <div class="cellLeft10"><input type="button" class="btnbrn" value="MAX/MIN" style="cursor:pointer" onclick="parent.location= '<?php echo $hilowbtn ?>' " /></div>
         <?php } ?>
         <div class="cellLeft40"><?php echo $hilowbtntxt; ?></div>
      </div>
<!-- Button RAIN -->
      <div  class="rowCenter">
         <div class="cellLeft10"><input type="button" class="btnbrn" style="cursor:pointer" value="PIOGGIA" id="rainbtnt"/></div>
         <div class="cellLeft40"><span lang="it">Grafico Pioggia 24 ore</span></div>
<!-- Button TIME -->
         <?php if($timebtntxt == "") { ?>
            <div class="cellLeft10"><input type="button" class="btnbrn" value="DATI" disabled="disabled" /></div>
         <?php } else if ($taboption===1||$taboption===3) { ?>
            <?php if ($showtooltip) { ?>
               <div class="cellLeft10"> <div data-tip="NewTab"><input type="button" class="btnbrn" value="DATI" style="cursor:pointer" onclick="window.open('<?php echo $timebtn ?>')" /></div></div>
            <?php } else { ?>
               <div class="cellLeft10"><input type="button" class="btnbrn" value="DATI" style="cursor:pointer" onclick="window.open('<?php echo $timebtn ?>')" /></div>
            <?php } ?>
         <?php } else { ?>
            <div class="cellLeft10"><input type="button" class="btnbrn" value="DATI" style="cursor:pointer" onclick="parent.location= '<?php echo $timebtn ?>' " /></div>
         <?php } ?>
         <div class="cellLeft40"><?php echo $timebtntxt; ?></div>
      </div>
<!-- Button BAR -->
      <div  class="rowCenter">
         <div class="cellLeft10"><input type="button" class="btnbrn" style="cursor:pointer" value="BAROM" id="barbtnt"/></div>
         <div class="cellLeft40"><span lang="it">Grafico Pressione Barometrica 24 ore</span></div>
<!-- Button DONE -->
         <?php if($donebtntxt == "") { ?>
            <div class="cellLeft10"><input type="button" class="btnbrn" value="FINE" disabled="disabled" /></div>
         <?php } else if ($taboption===1||$taboption===3) { ?>
            <?php if ($showtooltip) { ?>
               <div class="cellLeft10"> <div data-tip="NewTab"><input type="button" class="btnbrn" value="FINE" style="cursor:pointer" onclick="window.open('<?php echo $donebtn ?>')" /></div></div>
            <?php } else { ?>
               <div class="cellLeft10"><input type="button" class="btnbrn" value="FINE" style="cursor:pointer" onclick="window.open('<?php echo $donebtn ?>')" /></div>
            <?php } ?>
         <?php } else { ?>
             <div class="cellLeft10"><input type="button" class="btnbrn" value="FINE" style="cursor:pointer" onclick="parent.location= '<?php echo $donebtn ?>' " /></div>
         <?php } ?>
         <div class="cellLeft40"><?php echo $donebtntxt; ?></div>
      </div>
   </div><!-- END class="buttonTable" -->
</div><!-- END class="content" -->
</div></div></div>