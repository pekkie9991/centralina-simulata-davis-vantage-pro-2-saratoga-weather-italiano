<?php
// ABILITA LA DIAGNOSTICA DEI PARSE ERROR
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$davconvp2ver = "davconvp2CUmx.php - silveracorn.nz MXUI website - Version MX_2.1.3 - 16-Sep-2020";
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
$page_id = "davconvp2";
$siteRoot = '../../';
$sitePage = static function ($file) use ($siteRoot) {
    return $siteRoot . ltrim($file, '/');
};
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
<title>Console Davis VP2 - Meteo Limbiate</title>
<meta name="description" content="Console meteo Davis Vantage Pro 2 in tempo reale - Limbiate Villaggio del Sole">
<meta http-equiv="refresh" content="<?php echo $metrefresh; ?>" />
<!-- Bootstrap CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- jQuery CDN (needed before davcon scripts) -->
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
  <h1>&#127968; Console Davis Vantage Pro 2</h1>
  <a href="<?php echo htmlspecialchars($sitePage('wxindex.php'), ENT_QUOTES, 'UTF-8'); ?>">&#8592; Torna al Sito</a>
</div>
<!-- <?php echo $davconvp2ver ?> -->
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
$showsolar	=	'Y';	// Y or N 
$showuv		=	'Y';	// Y or N
$vpstormrain = 'Y';     // <--- AGGIUNGI QUESTA RIGA DI SICUREZZA
$stnnumbrs	= '1&nbsp;' . ' &nbsp;' . ' &nbsp;' . ' &nbsp;' . ' &nbsp;' . ' &nbsp;' . ' &nbsp;' . ' &nbsp;';
$showstnnum = true;	// $showstnnum = true; OR $showstnnum = false;
$showantenna= true;	// $showantenna = true; OR $showantenna = false;
// Settings for timing of 'live' image data rotate and compass pointer refresh behaviour
$itimeout	  = 2;	// time between image updates in seconds
$windrotate	  = 2;	// 1 = Wind speed only in wind rose, 2 = wind speed -> wind direction
$dewrotate	  = 7;	
$timeformat	  = 1;	// 1 = Time display in 'metric' format hh:mm
$dateformat	  = 1;	// 1 = Date display in 'metric' format dd/mm
$showupdate	  = 1;	
$showage		= 1;		
$zambretti	= 0;		// 0 = Use Station forecast

$fcastbtn		= $sitePage('gauges.php');			// forecast button
	$WxCenbtn	= $fcastbtn;				// WXcen button - for Vue
$fcastbtntxt	=	"<span lang='it'>Strumenti</span>";		// Strumenti/Gauges in Italiano
$WxCenbtntxt	=	$fcastbtntxt;			

$graphbtn		=	$sitePage('charts.php');		// graph button
$graphbtntxt	=	"<span lang='it'>Grafici</span>";		

$hilowbtn		=	$sitePage('records.php');		// hilow button
$hilowbtntxt	=	"<span lang='it'>Record Storici e Mensili</span>";	

$alarmbtn		=	$sitePage('todayyest.php');	// alarm button
	$timebtn		=	$alarmbtn;				

$alarmbtntxt	=	"<span lang='it'>Dati Oggi/Ieri</span>";	
	$timebtntxt =	$alarmbtntxt;		

$donebtn		  = $sitePage('now.php');				// done button
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
require_once('./davconvp2CUmx-inc.php');
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
    Stazione Meteo Limbiate &mdash; Villaggio del Sole &mdash; Davis Vantage Pro 2<br>
    Dati in tempo reale da WeatherLink &bull; Aggiornamento automatico ogni 5 minuti
</div>
</div><!-- .davcon-page-wrapper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>