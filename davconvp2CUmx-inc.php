<!-- davconvp2CUmx-inc.php - silveracorn.nz MXUI website - Version MX_2.1.3 - 16-Sep-2020 -->
<?php
// MX version 3089
// BEGIN Cumulus Graph Data
if (file_exists($graphurl)) {
	for($i=0;$i<26;$i++){	// 26 data points - vp2 does not use the first one as only 25 points on graph
		$tmcb[] = (time()+($i*3600)*1000); // $tmcb == TiMe CB
	}
} else {echo "&nbsp;&nbsp;$graphurl NOT Found!"; Return;} // for Cumulus graph data
$graphurl = implode(' ', file($graphurl));
$c = preg_split('/\s+/', trim($graphurl));
$c = is_array($c) ? array_values($c) : [];
$c = array_pad($c, 161, '0');
if (!is_numeric($c[157]) || (float)$c[157] <= 0) {
	$c[157] = '300';
}
if (!preg_match('/^\d{2}:\d{2}$/', (string)$c[160])) {
	$c[160] = date('H:i');
}
// $temp 25 datapoints for last 24 Hr temp graph 1/hr plus one RH end
$temp = '[['.$tmcb[0].','.$c[1].'],['.$tmcb[1].','.$c[2].'],['.$tmcb[2].','.$c[3].'],['.$tmcb[3].','.$c[4].'],['.$tmcb[4].','.$c[5].'],['.$tmcb[5].','.$c[6].'],['.$tmcb[6].','.$c[7].'],['.$tmcb[7].','.$c[8].'],['.$tmcb[8].','.$c[9].'],['.$tmcb[9].','.$c[10].'],['.$tmcb[10].','.$c[11].'],['.$tmcb[11].','.$c[12].'],['.$tmcb[12].','.$c[13].'],['.$tmcb[13].','.$c[14].'],['.$tmcb[14].','.$c[15].'],['.$tmcb[15].','.$c[16].'],['.$tmcb[16].','.$c[17].'],['.$tmcb[17].','.$c[18].'],['.$tmcb[18].','.$c[19].'],['.$tmcb[19].','.$c[20].'],['.$tmcb[20].','.$c[21].'],['.$tmcb[21].','.$c[22].'],['.$tmcb[22].','.$c[23].'],['.$tmcb[23].','.$c[24].'],['.$tmcb[24].','.$c[25].']]';
// $hum 25 datapoints for last 24 Hr humidity graph 1/hr plus one RH end
$hum = '[['.$tmcb[0].','.$c[27].'],['.$tmcb[1].','.$c[28].'],['.$tmcb[2].','.$c[29].'],['.$tmcb[3].','.$c[30].'],['.$tmcb[4].','.$c[31].'],['.$tmcb[5].','.$c[32].'],['.$tmcb[6].','.$c[33].'],['.$tmcb[7].','.$c[34].'],['.$tmcb[8].','.$c[35].'],['.$tmcb[9].','.$c[36].'],['.$tmcb[10].','.$c[37].'],['.$tmcb[11].','.$c[38].'],['.$tmcb[12].','.$c[39].'],['.$tmcb[13].','.$c[40].'],['.$tmcb[14].','.$c[41].'],['.$tmcb[15].','.$c[42].'],['.$tmcb[16].','.$c[43].'],['.$tmcb[17].','.$c[44].'],['.$tmcb[18].','.$c[45].'],['.$tmcb[19].','.$c[46].'],['.$tmcb[20].','.$c[47].'],['.$tmcb[21].','.$c[48].'],['.$tmcb[22].','.$c[49].'],['.$tmcb[23].','.$c[50].'],['.$tmcb[24].','.$c[51].']]';
// $wind 25 datapoints for last 24 Hr windspeed graph 1/hr plus one RH end
$wind = '[['.$tmcb[0].','.$c[53].'],['.$tmcb[1].','.$c[54].'],['.$tmcb[2].','.$c[55].'],['.$tmcb[3].','.$c[56].'],['.$tmcb[4].','.$c[57].'],['.$tmcb[5].','.$c[58].'],['.$tmcb[6].','.$c[59].'],['.$tmcb[7].','.$c[60].'],['.$tmcb[8].','.$c[61].'],['.$tmcb[9].','.$c[62].'],['.$tmcb[10].','.$c[63].'],['.$tmcb[11].','.$c[64].'],['.$tmcb[12].','.$c[65].'],['.$tmcb[13].','.$c[66].'],['.$tmcb[14].','.$c[67].'],['.$tmcb[15].','.$c[68].'],['.$tmcb[16].','.$c[69].'],['.$tmcb[17].','.$c[70].'],['.$tmcb[18].','.$c[71].'],['.$tmcb[19].','.$c[72].'],['.$tmcb[20].','.$c[73].'],['.$tmcb[21].','.$c[74].'],['.$tmcb[22].','.$c[75].'],['.$tmcb[23].','.$c[76].'],['.$tmcb[24].','.$c[77].']]';
// $rain 25 datapoints for last 24 Hr rain graph 1/hr plus one RH end
$rain = '[['.$tmcb[0].','.$c[79].'],['.$tmcb[1].','.$c[80].'],['.$tmcb[2].','.$c[81].'],['.$tmcb[3].','.$c[82].'],['.$tmcb[4].','.$c[83].'],['.$tmcb[5].','.$c[84].'],['.$tmcb[6].','.$c[85].'],['.$tmcb[7].','.$c[86].'],['.$tmcb[8].','.$c[87].'],['.$tmcb[9].','.$c[88].'],['.$tmcb[10].','.$c[89].'],['.$tmcb[11].','.$c[90].'],['.$tmcb[12].','.$c[91].'],['.$tmcb[13].','.$c[92].'],['.$tmcb[14].','.$c[93].'],['.$tmcb[15].','.$c[94].'],['.$tmcb[16].','.$c[95].'],['.$tmcb[17].','.$c[96].'],['.$tmcb[18].','.$c[97].'],['.$tmcb[19].','.$c[98].'],['.$tmcb[20].','.$c[99].'],['.$tmcb[21].','.$c[100].'],['.$tmcb[22].','.$c[101].'],['.$tmcb[23].','.$c[102].'],['.$tmcb[24].','.$c[103].']]';
// $baro 25 datapoints for last 24 Hr baro graph 1/hr plus one RH end
$baro = '[['.$tmcb[0].','.$c[105].'],['.$tmcb[1].','.$c[106].'],['.$tmcb[2].','.$c[107].'],['.$tmcb[3].','.$c[108].'],['.$tmcb[4].','.$c[109].'],['.$tmcb[5].','.$c[110].'],['.$tmcb[6].','.$c[111].'],['.$tmcb[7].','.$c[112].'],['.$tmcb[8].','.$c[113].'],['.$tmcb[9].','.$c[114].'],['.$tmcb[10].','.$c[115].'],['.$tmcb[11].','.$c[116].'],['.$tmcb[12].','.$c[117].'],['.$tmcb[13].','.$c[118].'],['.$tmcb[14].','.$c[119].'],['.$tmcb[15].','.$c[120].'],['.$tmcb[16].','.$c[121].'],['.$tmcb[17].','.$c[122].'],['.$tmcb[18].','.$c[123].'],['.$tmcb[19].','.$c[124].'],['.$tmcb[20].','.$c[125].'],['.$tmcb[21].','.$c[126].'],['.$tmcb[22].','.$c[127].'],['.$tmcb[23].','.$c[128].'],['.$tmcb[24].','.$c[129].']]';
// $solr 25 datapoints for last 24 Hr solar graph 1/hr plus one RH end
$solr = '[['.$tmcb[0].','.$c[131].'],['.$tmcb[1].','.$c[132].'],['.$tmcb[2].','.$c[133].'],['.$tmcb[3].','.$c[134].'],['.$tmcb[4].','.$c[135].'],['.$tmcb[5].','.$c[136].'],['.$tmcb[6].','.$c[137].'],['.$tmcb[7].','.$c[138].'],['.$tmcb[8].','.$c[139].'],['.$tmcb[9].','.$c[140].'],['.$tmcb[10].','.$c[141].'],['.$tmcb[11].','.$c[142].'],['.$tmcb[12].','.$c[143].'],['.$tmcb[13].','.$c[144].'],['.$tmcb[14].','.$c[145].'],['.$tmcb[15].','.$c[146].'],['.$tmcb[16].','.$c[147].'],['.$tmcb[17].','.$c[148].'],['.$tmcb[18].','.$c[149].'],['.$tmcb[19].','.$c[150].'],['.$tmcb[20].','.$c[151].'],['.$tmcb[21].','.$c[152].'],['.$tmcb[22].','.$c[153].'],['.$tmcb[23].','.$c[154].'],['.$tmcb[24].','.$c[155].']]';
// extract max & min values for each graph data type to display max & min values as graph scale is not constant but scales to data range
$t = array_slice($c, 1, 25);
	$tmax = max($t);
	$tmin = min($t);
$h = array_slice($c, 27, 25);
	$hmax = max($h);
	$hmin = min($h);
$w = array_slice($c, 53, 25);
	$wmax = max($w);
	$wmin = min($w);
$r = array_slice($c, 79, 25);
	$rmax = round(max($r),1);
	$rmin = round(min($r),1);
$b = array_slice($c, 105, 25);
	$bmax = round(max($b),1);
	$bmin = round(min($b),1);
$s = array_slice($c, 131, 25);
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
		document.getElementById('vp2console').style.backgroundImage = "url('./davcon/vp2_console.png')";
	else
		document.getElementById('vp2console').style.backgroundImage = "url('./davcon/vp2_console_lit.png')";
}
</script>
<div id="content">
	<noscript><div class="warning"><h1><span lang="it">ABILITA JAVASCRIPT PER GLI AGGIORNAMENTI IN TEMPO REALE!</span></h1></div></noscript>
<br />
<div id="main-copy">
<div style="text-align:left; margin:0 auto; width:700px;">
<div class="vp2console" id="vp2console">

<!-- WIND COMPASS TEXT -->
<span class="small" lang="it" style= "top: 57px; left: 50px;">VENTO</span>

<!-- FORECAST ICON -->
<span class="vajax" id="cajaxicon" style="top: 53px; left: 200px;"></span>

<!-- MOON ICON -->
<span class="vajax" id="cajaxmoon" style="top: 53px; left: 270px;"></span>

<!-- TIME DATE -->
<span class="vajax" id="cajaxhhmm" style="font-size: 24px; top: 57px; right: 320px;"></span>
<span class="small" id="cajaxampm" style="top: 70px; right: 301px;"></span>
<span class="vajax" id="cajaxddmo" style="font-size: 24px; top: 57px; right: 245px;"></span>

<!-- WIND -->
<div class="vajax" id="wdir" style=" top: 78px; left: 72px; position: relative;">
<div id="windarrow" style= "font-size: 15px; padding: 0px; position: absolute; top: 34px; left: 0px; transform-origin: 47px 50%">&#10148;</div>
</div>
<span class="vajax" id="cajaxwind" style="top: 101px; right: 556px;"></span>
<span class="small" id="cajaxwindu" style="top:138px; left: 108px;"></span>
<span class="small" id="cajaxwinddu" style="top:110px; left: 146px;"></span>

<!-- TEMP OUTSIDE -->
<span class="small" lang="it" style= "top: 95px; left: 210px;">TEMP EST</span>
<span class="vajax" id="cajaxtemp" style="top: 99px; right: 440px;"></span>
<span class="smallb" style="top:110px; left: 265px;"><?php echo $tempunit; ?></span>

<!-- HUMIDITY OUTSIDE -->
<span class="small" lang="it" style= "top: 95px; left: 285px;">UMID EST</span>
<span class="vajax" id="cajaxhumidity" style="top: 99px;	 right: 377px;"></span>
<span class="smallb" style="top:110px; left:325px;">%</span>

<!-- BAROMETER -->
<span class="small" style="top:95px; right:265px;" lang="it">BAROMETRO</span>
<span class="vajax" id="cajaxbaroarrow" style="top:95px; left:443px; font-size: 12px; z-index:100;">&#10148;</span>
<span class="vajax" id="cajaxbaro" style="top: 99px; right: 265px;"></span>
<span class="smallb" style="top:118px; left: 440px;"><?php echo $pressunit; ?></span>

<!-- TEMP INSIDE -->
<span class="small" id="itemp1" lang="it" style="top:153px; left:215px; display: none;">TEMP INT</span>
<span class="small" id="itemp2" lang="it" style="top:153px; left:215px; display: none;">SOLARE W/m2</span>
<span class="vajax" id="cajaxitemp" style="top: 158px; right: 440px;"></span>
<span class="smallb" id="itempinuom" style="top:167px; left: 265px;"></span>

<!-- HUMIDITY INSIDE -->
<span class="small" id="ihum1" lang="it" style="top:153px; right:350px; display: none;">UMID INT</span>
<span class="small" id="ihum2" lang="it" style="top:153px; right:350px; display: none;">UV</span>
<span class="vajax"	id="cajaxihumidity" style="top: 158px; right: 355px;"></span>
<span class="small"	id="ihumuom" style="top:168px; right:342px;"></span>
<span class="small"	id="index" style="top:172px; right:325px;"></span>

<!-- APPARENT TEMP -->
<span class="small" id="app1" lang="it" style="top:153px; right:265px; display: none;">APPARENTE</span>
<span class="small" id="app2" lang="it" style="top:153px; right:265px; display: none;">RUGIADA</span>
<span class="small" id="app3" lang="it" style="top:153px; right:265px; display: none;">HUMIDEX</span>
<span class="small" id="app4" lang="it" style="top:153px; right:265px; display: none;">WIND CHILL</span>
<span class="small" id="app5" lang="it" style="top:153px; right:265px; display: none;">IND. CALORE</span>
<span class="vajax"	id="cajaxapp" style="top: 158px; right: 265px;"></span>
<span class="smallb" id="itempuom" style="top:167px; left: 440px;"><?php echo $tempunit; ?></span>

<!-- DAILY RAIN -->
<span class="small" id="rrd1" lang="it" style="top:212px; right:410px; display: none;">PIOGGIA GIORN.</span>
<span class="small" id="rrd2" lang="it" style="top:212px; right:415px; display: none;">ET</span>
<span class="small" id="rrd3" lang="it" style="top:212px; right:415px; display: none;">TEMPESTA</span>
<span class="vajax" id="cajaxrain" style="top:216px; right:415px;"></span>
<span class="small" style="top:238px; left:288px;"><?php echo $rainunit; ?></span>

<!-- Is Raining or snow ICON -->
<span class="vajax" id="cajaxumbr" style="top:212px; right:360px;"></span>

<!-- RAIN RATE -->
<span class="small" id="rrth" style="top:238px; left:433px;"><?php echo $rainunit; ?></span>
<span class="small" id="rrh" style= "top:238px; left:453px; display:none;" lang="it">/ora</span>
<span class="small" id="rrh1" lang="it" style="top:212px; right:270px;">INTENS. PIOGGIA</span>
<span class="small" id="rrh2" lang="it" style="top:212px; right:270px;">PIOGGIA ORA</span>
<span class="small" id="rrh3" lang="it" style="top:212px; right:270px;">PIOGGIA MESE</span>
<span class="small" id="rrh4" lang="it" style="top:212px; right:270px;">PIOGGIA ANNO</span>
<span class="vajax" id="cajaxrainratehr" style="top:216px; right:270px;"></span>

<!-- STATION NUMBERS -->
<?php if ($showstnnum) { ?>
	<span class="small" style="position:absolute; top:254px; left:280px;"><span lang="it">STAZIONE N.</span><?php echo $stnnumbrs; ?></span>
<?php } ?>
<!-- ANTENNA ICON -->
<?php if ($showantenna) { ?>
	<span class="vajax" id="cajaxanten" style="font-size: 24px; top:262px; left:445px;"></span>
<?php } ?>

<!-- FORECAST TEXT -->
<div id="vp2_scroller_container">
 <div class="jscroller2_left jscroller2_speed-50 jscroller2_mousemove jscroller2_ignoreleave jscroller2_dynamic">
	<span lang= "it"><?php echo $forecast; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
 <div class="jscroller2_left_endless jscroller2">
	<span lang= "it"><?php echo $forecast; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
</div>

<!-- GRAPH LABEL -->
<span class="small" id="grhr24" lang="it" style="position:absolute; top:178px; left:55px;">v 24 Ore</span>
<span class="small" id="grhr1"  lang="it" style="position:absolute; top:178px; right:512px;">Ora v</span>
<span class="small" id="grlab1" lang="it" style="position:absolute; top:178px; left:110px; display: inline-block;">TEMP</span>
<span class="small" id="grlab2" lang="it" style="position:absolute; top:178px; left:110px; display: none;">UMID</span>
<span class="small" id="grlab3" lang="it" style="position:absolute; top:178px; left:110px; display: none;">VENTO</span>
<span class="small" id="grlab4" lang="it" style="position:absolute; top:178px; left:110px; display: none;">PIOGGIA</span>
<span class="small" id="grlab5" lang="it" style="position:absolute; top:178px; left:110px; display: none;">BAROM</span>
<span class="small" id="grlab6" lang="it" style="position:absolute; top:178px; left:106px; display: none;">SOLARE</span>
<span class="small" lang="it" style="position:absolute; top:254px; left:55px;">Max</span>
<span class="small" id="grmax" style="position:absolute; top:254px; left:77px;"><?php echo $tmax; ?></span>
<span class="small" lang="it" style="position:absolute; top:254px; right:511px;">Min</span>
<span class="small" id="grmin" style="position:absolute; top:254px; right:532px;"><?php echo $tmin; ?></span>

<!-- GRAPH WINDOW -->
<div id="placeholder" style="height:60px; width:130px; position:absolute; top:193px; left:57px;"></div>

<!-- BUTTONS Console LEFT column IMAGE -->
<div id="tempbtn"	 style="cursor:pointer; height:22px; width:70px; position:absolute; top:53px; left:510px;"></div>
<div id="humbtn"	 style="cursor:pointer; height:22px; width:70px; position:absolute; top:95px; left:510px;"></div>
<div id="windbtn"	 style="cursor:pointer; height:22px; width:70px; position:absolute; top:137px; left:510px;"></div>
<?php if ($showsolar == 'Y') { ?>
<div id="solarbtn" style="cursor:pointer; height:22px; width:70px; position:absolute; top:179px; left:510px;"></div>
<?php } ?>
<div id="rainbtn"	 style="cursor:pointer; height:22px; width:70px; position:absolute; top:221px; left:510px;"></div>
<div id="barbtn"	 style="cursor:pointer; height:22px; width:70px; position:absolute; top:262px; left:510px;"></div>

<!-- BUTTONS Console RIGHT column IMAGE -->
<div id="lampsbtn" onclick="BackLight()" style="cursor:pointer; height:22px; width:70px; position:absolute; top:31px; left:595px"></div>
<?php if($fcastbtntxt != "") { ?>	
	<?php if ($taboption===2||$taboption===3) { ?><div id="fcastbtn"
		<?php if ($showtooltip) { ?> data-tip="NewTab" onclick="window.open('<?php echo $fcastbtn ?>')"
		<?php } else { ?> onclick="window.open('<?php echo $fcastbtn ?>')" <?php } ?>
	<?php } else { ?><div id="fcastbtn" onclick="top.location.href=fcastbtn" <?php } ?>
	style="cursor:pointer; height:22px; width:70px; position:absolute; top:72px; left:600px"></div>
<?php }	if($graphbtntxt != "") { ?>	
	<?php if ($taboption===2||$taboption===3) { ?><div id="graphbtn"
		<?php if ($showtooltip) { ?> data-tip="NewTab" onclick="window.open('<?php echo $graphbtn ?>')"
		<?php } else	{ ?> onclick="window.open('<?php echo $graphbtn ?>')" <?php } ?>
	<?php } else { ?><div id="graphbtn" onclick="top.location.href=graphbtn" <?php } ?>
	style="cursor:pointer; height:22px; width:70px; position:absolute; top:115px; left:600px"></div>
<?php }	if($hilowbtntxt != '') { ?>	
	<?php if ($taboption===2||$taboption===3) { ?><div id="hilowbtn" 
		<?php if ($showtooltip) { ?> data-tip="NewTab" onclick="window.open('<?php echo $hilowbtn ?>')"
		<?php } else { ?> onclick="window.open('<?php echo $hilowbtn ?>')" <?php } ?>
	<?php } else { ?><div id="hilowbtn" onclick="top.location.href=hilowbtn" <?php } ?>
	style="cursor:pointer; height:22px; width:70px; position:absolute; top:157px; left:600px"></div>
<?php }	if($alarmbtntxt != '') { ?>	
	<?php if ($taboption===2||$taboption===3) { ?><div id="alarmbtn"
		<?php if ($showtooltip) { ?> data-tip="NewTab" onclick="window.open('<?php echo $alarmbtn ?>')"
		<?php } else { ?> onclick="window.open('<?php echo $alarmbtn ?>')" <?php } ?>
	<?php } else { ?><div id="alarmbtn" onclick="top.location.href=alarmbtn" <?php } ?>
	style="cursor:pointer; height:22px; width:70px; position:absolute; top:200px; left:600px"></div>
<?php } if($donebtntxt != '') { ?>	 
	<?php if ($taboption===2||$taboption===3) { ?><div id="donebtn"
		<?php if ($showtooltip) { ?> data-tip="NewTab" onclick="window.open('<?php echo $donebtn ?>')"
		<?php } else { ?> onclick="window.open('<?php echo $donebtn ?>')" <?php } ?>
	<?php } else { ?>
	<div id="donebtn" onclick="top.location.href=donebtn" <?php } ?>
	style="cursor:pointer; height:22px; width:70px; position:absolute; top:243px; left:600px"></div>
<?php } ?>	 
</div> <!-- END id="content" -->
<!-- preload main image -->
<div id="preload" style="display:none"><img src="./davcon/vp2_console.png" alt=""></div>
<!-- showupdate and show age -->
<div class="content">
	<div class="buttonTable">
		<div class="rowCenter">
		<?php if ($showupdate === 1) { ?>
			<div class="cellLeft50"><span lang="it">Prossimo agg. grafico e previsioni</span> @ ~ <?php echo $nextupdate; ?></div>
		<?php } if ($showupdate === 2) { ?>
			<div class="cellLeft50"><span lang="it">Grafico e previsioni aggiornati</span> @ <?php echo $timedc; ?></div>
		<?php } if ($showage === 1) { ?>
			<div class="cellLeft50">&nbsp;&nbsp;&nbsp;<span lang="it">Prossimo agg. in tempo reale in</span>&nbsp;~&nbsp;<b><span id="ajaxcounterdcdn"></span></b>&nbsp;<span lang="it">sec</span></div>
		<?php } if ($showage === 2) { ?>
			<div class="cellLeft50">&nbsp;&nbsp;&nbsp;<span lang="it">Dati in tempo reale aggiornati&nbsp;</span><b><span id="ajaxcounterdcup"></b>&nbsp;<span lang="it">sec fa</span></span></div>
		<?php } ?>
		</div>
	</div>
	<div class="buttonTable">
		<div class="rowCenter">
			<div class="cellLeft50"> <b><span lang="it">Clicca sui pulsanti della console o della tabella</span></b> </div>
		</div>
	</div>

<!-- BUTTONS TABLE below image -->
	<div class="buttonTable">
		<div class="rowCenter"> <!-- ROW 1 -->
<!-- Button SOLAR -->
<?php if ($showsolar == 'Y') { ?>
			<div class="cellLeft10"><input type="button" class="btngrey" style="cursor:pointer" value="SOLARE" id="solarbtnt"/></div>
			<div class="cellLeft40"><span lang="it">Grafico Radiazione Solare 24 ore</span></div>
<?php } else { ?>
			<div class="cellLeft10"> </div>
			<div class="cellLeft40"> </div>
<?php } ?>
<!-- Button BACKLIGHT -->
			<div class="cellLeft10"><input type="button" class="btngrey"	onclick="BackLight()" style="cursor:pointer" value="RETRO" /></div>
			<div class="cellLeft40"><span lang="it">Accendi/Spegni Retroilluminazione</span></div>
		</div>

<!-- Button TEMP --> <!-- ROW 2 -->
		<div	class="rowCenter">
			<div class="cellLeft10"><input type="button" class="btngrey" style="cursor:pointer" value="TEMP" id="tempbtnt" /></div>
			<div class="cellLeft40"><span lang="it">Grafico Temperatura Esterna 24 ore</span></div>
<!-- Button FCAST -->
			<?php if($fcastbtntxt == "") { ?>
				<div class="cellLeft10"><input type="button" class="btngrey" value="PREV." disabled /></div>
			<?php } else if ($taboption===1||$taboption===3) { ?>
				<?php if ($showtooltip) { ?>
					<div class="cellLeft10"> <div data-tip="NewTab"><input type="button" class="btngrey" value="PREV." style="cursor:pointer"
													onclick="window.open('<?php echo $fcastbtn ?>')" /></div></div>
				<?php } else { ?>
					<div class="cellLeft10"> <input type="button" class="btngrey" value="PREV." style="cursor:pointer"
													onclick="window.open('<?php echo $fcastbtn ?>')" /></div>
				<?php } ?>
			<?php } else { ?>
				<div class="cellLeft10"> <input type="button" class="btngrey" value="PREV." style="cursor:pointer"
													onclick="parent.location= '<?php echo $fcastbtn ?>' " /></div>
			<?php } ?>
			<div class="cellLeft40"><?php echo $fcastbtntxt; ?></div>
		</div>

<!-- Button HUM --> <!-- ROW 3 -->
		<div	class="rowCenter">
			<div class="cellLeft10"><input type="button" class="btngrey" style="cursor:pointer" value="UMID" id="humbtnt"/></div>
			<div class="cellLeft40"><span lang="it">Grafico Umidità Esterna 24 ore</span></div>
<!-- Button GRAPH -->
			<?php if($graphbtntxt == "") { ?>
				<div class="cellLeft10"><input type="button" class="btngrey" value="GRAFICI" disabled="disabled" /></div>
			<?php } else if ($taboption===1||$taboption===3) { ?>
				<?php if ($showtooltip) { ?>
					<div class="cellLeft10"> <div data-tip="NewTab"><input type="button" class="btngrey" value="GRAFICI" style="cursor:pointer"
													onclick="window.open('<?php echo $graphbtn ?>')" /></div></div>
				<?php } else { ?>
					<div class="cellLeft10"><input type="button" class="btngrey" value="GRAFICI" style="cursor:pointer"
													onclick="window.open('<?php echo $graphbtn ?>')" /></div>
				<?php } ?>
			<?php } else { ?>
				<div class="cellLeft10"><input type="button" class="btngrey" value="GRAFICI" style="cursor:pointer"
													onclick="parent.location= '<?php echo $graphbtn ?>' " /></div>
			<?php } ?>
			<div class="cellLeft40"><?php echo $graphbtntxt; ?></div>
		</div>

<!-- Button WIND --> <!-- ROW 4 -->
		<div	class="rowCenter">
			<div class="cellLeft10"><input type="button" class="btngrey" style="cursor:pointer" value="VENTO" id="windbtnt"/></div>
			<div class="cellLeft40"><span lang="it">Grafico Velocità Vento 24 ore</span></div>
<!-- Button HI/LOW -->
			<?php if($hilowbtntxt == "") { ?>
				<div class="cellLeft10"><input type="button" class="btngrey" value="MAX/MIN" disabled="disabled" /></div>
			<?php } else if ($taboption===1||$taboption===3) { ?>
				<?php if ($showtooltip) { ?>
					<div class="cellLeft10"> <div data-tip="NewTab"><input type="button" class="btngrey" value="MAX/MIN" style="cursor:pointer"
													onclick="window.open('<?php echo $hilowbtn ?>')" /></div></div>
				<?php } else { ?>
					<div class="cellLeft10"><input type="button" class="btngrey" value="MAX/MIN" style="cursor:pointer"
													onclick="window.open('<?php echo $hilowbtn ?>')" /></div>
				<?php } ?>
			<?php } else { ?>
				<div class="cellLeft10"><input type="button" class="btngrey" value="MAX/MIN" style="cursor:pointer"
													onclick="parent.location= '<?php echo $hilowbtn ?>' " /></div>
			<?php } ?>
			<div class="cellLeft40"><?php echo $hilowbtntxt; ?></div>
		</div>

<!-- Button RAIN --> <!-- ROW 5 -->
		<div	class="rowCenter">
			<div class="cellLeft10"><input type="button" class="btngrey" style="cursor:pointer" value="PIOGGIA" id="rainbtnt"/></div>
			<div class="cellLeft40"><span lang="it">Grafico Pioggia 24 ore</span></div>
<!-- Button ALARM -->
			<?php if($alarmbtntxt == "") { ?>
				<div class="cellLeft10"><input type="button" class="btngrey" value="DATI" disabled="disabled" /></div>
			<?php } else if ($taboption===1||$taboption===3) { ?>
				<?php if ($showtooltip) { ?>
					<div class="cellLeft10"> <div data-tip="NewTab"><input type="button" class="btngrey" value="DATI" style="cursor:pointer"
													onclick="window.open('<?php echo $alarmbtn ?>')" /></div></div>
				<?php } else { ?>
					<div class="cellLeft10"><input type="button" class="btngrey" value="DATI" style="cursor:pointer"
													onclick="window.open('<?php echo $alarmbtn ?>')" /></div>
				<?php } ?>
			<?php } else { ?>
				<div class="cellLeft10"><input type="button" class="btngrey" value="DATI" style="cursor:pointer"
													onclick="parent.location= '<?php echo $alarmbtn ?>' " /></div>
			<?php } ?>
			<div class="cellLeft40"><?php echo $alarmbtntxt; ?></div>
		</div>

<!-- Button BAR --> <!-- ROW 6 -->
		<div	class="rowCenter">
			<div class="cellLeft10"><input type="button" class="btngrey" style="cursor:pointer" value="BAROM" id="barbtnt"/></div>
			<div class="cellLeft40"><span lang="it">Grafico Pressione Barometrica 24 ore</span></div>
<!-- Button DONE -->
			<?php if($donebtntxt == "") { ?>
				<div class="cellLeft10"><input type="button" class="btngrey" value="FINE" disabled="disabled" /></div>
			<?php } else if ($taboption===1||$taboption===3) { ?>
				<?php if ($showtooltip) { ?>
					<div class="cellLeft10"> <div data-tip="NewTab"><input type="button" class="btngrey" value="FINE" style="cursor:pointer"
													onclick="window.open('<?php echo $donebtn ?>')" /></div></div>
				<?php } else { ?>
					<div class="cellLeft10"><input type="button" class="btngrey" value="FINE" style="cursor:pointer"
													onclick="window.open('<?php echo $donebtn ?>')" /></div>
				<?php } ?>
			<?php } else { ?>
				 <div class="cellLeft10"><input type="button" class="btngrey" value="FINE" style="cursor:pointer"
				 									onclick="parent.location= '<?php echo $donebtn ?>' " /></div>
			<?php } ?>
			<div class="cellLeft40"><?php echo $donebtntxt; ?></div>
		</div>
   </div><!-- END class="buttonTable" -->
</div><!-- END class="content" -->
</div></div></div>