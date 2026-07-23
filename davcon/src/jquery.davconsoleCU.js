/* beteljuice mods start #773 - wind / baro arrows and ajax call
// silveracorn.nz MXUI website - Version MX_2.1.2 - 1-Oct-2020
// MX version 3089
Settings file for JQuery plugin for rendering gauges
* Initial create by Henkka (as jquery.console.js v. 1.0), Jan 2010
* Cumulus customisation by BCJKiwi, May 2013	 http://silveracorn.nz/weather/
* reworked completely for Cumulus, added back WD.
* auto-rotate rain, dew/apparent temp, and Temp in/ET UV Solar values.
* Added timeout and retry to make Datafile Get routine more reliable.
* added routine to display israining umbrella icon.
* turn off ET/UV/Solar rotation at night.
*** setting options in calling php script ***
* 1.1d  added imperial unit variants for rain decimal places, and for barotrend arrow
* 1.1e  revised barotrend arrow position code
* 2.0	  revised for php Ver CU2.0
* 2.2.1 revised for php Ver CU2.2.1 - recoded solar tests, added storm rain.
* 2.2.2 revised for php Ver CU2.2.2 - recoded windrose wind speed / degrees presentation.
* 2.2.4 revised for php Ver CU2.2.4
* 2.2.5 revised for php Ver CU2.2.5 - Added 'Metric' / 'US' date format option settings
* 2.3.0 revised for php Ver CU2.3.0 - recoded variable units conversion sections
* 2.4.0 revised for php Ver CU2.4.0 - added antenna icons for Vue, station number for VP2
*												- added 'X' signal lost for VP2
* 2.4.1 revised for php Ver CU2.4.1 - changed term to Storm Rain
* silveracorn.nz MXUI website - Version MX_2.1.1 - 1-Aug-2020 - Reworked for language translation
* silveracorn.nz MXUI website - Version MX_2.1.2 - 1-Oct-2020 - beteljuice added ' , ' to ' . ' decimal conversion
*																 - fixed storm rain in translated langs
*																 - rewrite of 'realtime' updates
*/
// Mike Challis' counter function (adapted by Ken True / BCJKiwi) *** time since ... not being used


$(function() {
	var avgwinddotsoffset = 14;
	var options = {};
	var data = '';
	var alreadyFetched = {};
	var cr = '';
	var windbutton = 0;
	var dayrainbutton = 0;
	var rainbutton = 0;
	var appbutton	= 0;
	var tempinbutton = 0;
	var huminbutton = 0;
	var antennaicon = 0;
	var crcache = {};

// Count up / down timers
	var reloadTimedc	 = realint;		  // realtint = "real time" file upload interval in millisecs
	var counterSecdcup = 0;				  // for MCHALLIS counter script from weather-watch.com (adapted by K. True) - not being used
	var counterSecdcdn = 0;				  // for MCHALLIS counter script from weather-watch.com (adapted by K. True)
	var updatesdc		 = 0;				  // update counter for limit by maxupdates
	var lastajaxtimeformatdc = 'unknown'; //used to reset the counter when a real update is done

	setInterval(function() { // countup
		var elementdc = document.getElementById("ajaxcounterdcdn");
		if (elementdc) {
			elementdc.innerHTML = reloadTimedc/1000 - counterSecdcdn;
			if (elementdc.innerHTML < 0) {elementdc.innerHTML = "?";} else {
				counterSecdcdn++;
			}
		}
	}, 1000);

	setInterval(function() { // countdown
		var elementdc = document.getElementById("ajaxcounterdcdn");
		var elementdc = document.getElementById("ajaxcounterdcup");
		if (elementdc) {
			elementdc.innerHTML = counterSecdcup + 1;
			counterSecdcup++;
		}
	}, 1000);

	update();
	function update() {

		//Reset data
		alreadyFetched = {};
		function onDataReceived(series) {

// ### CUMULUS REALTIME.TXT  ##################################
// We need some some values from realtime-x.txt
// split realtime-x.txt
				var cr = series.split('~');
// convert any decimal comma(s) [unconditional]
				var cr_size = cr.length;
				for(xyz = 2; xyz < cr_size; xyz++) {
					cr[xyz] = cr[xyz].replace(",", ".");
				}
// TIME AND DATE
				var dateonly  = cr[0];						// Cumulus realtime Date ALWAYS dd/mm/yy
				var timeonly  = cr[1];						// Cumulus realtime Time always hh:mm:ss
	// Time
				if (timeformat != 2) {						// Metric default
					var hourmin = timeonly.slice(0, 5); // for time as hh:mm - takes first 5 chars from hh:mm:ss
					var ampm = ' ';							// no am pm indicator
				} else {											// Imperial alternate
					var hh = timeonly.slice(0, 2);
					var mm = timeonly.slice(3, 5);
					var ampm = (hh > 11) ? 'pm' : 'am';
					if (hh > 12) {hh = hh - 12;}
					var hourmin = hh + ':' + mm;
				}
	// date
				if (dateformat != 2) {						// Metric default
					var daymon = dateonly.slice(0, 5);	// delivers date as dd/mm
				} else {											// Imperial option
					var daymon = dateonly.slice(3,5)+'/'+ dateonly.slice(0,2);	// delivers date as mm/dd
				}
				$("#cajaxddmo").html(daymon);
				$("#cajaxhhmm").html(hourmin);
				$("#cajaxampm").html(ampm);
// TEMP
				var crtemp = (cr[2] * 1).toFixed(1);	// Outside temp
				crtemp = crtemp.replace("-100.0", "-- ");
				$("#cajaxtemp").html(crtemp);
				var critemp = (cr[22] * 1).toFixed(1); // Inside temp
				critemp = critemp.replace("-100.0", "-- ");
				$("#cajaxitemp").html(critemp);
				var crdew  = (cr[4] * 1).toFixed(1);	// Dew point
				var crtempu = '&deg;'+ cr[14];
// APPARENT TEMP
				var crapp  =  cr[54];						// Apparent Temperature
				crapp = crapp.replace("-100.0", "-- ");
				$("#cajaxapp").html(crapp);
				var crchill = (cr[24] * 1).toFixed(1); // Wind Chill
				var crheat	= (cr[41] * 1).toFixed(1); // Heat Index
				var crhdex	= (cr[42] * 1).toFixed(1); // Humidex
// HUMIDITY
				var crhum = cr[3];							// Outside Humidity
				$("#cajaxhumidity").html(crhum);
				var crihum = cr[23];							// Inside Humidity
				$("#cajaxihumidity").html(crihum);
// WIND
				var cravg  =  cr[5];							// Wind speed (average)
				var crwspd = (cr[6] * 1).toFixed(1);	// Wind speed (latest)
				$("#cajaxwind").html(crwspd);
				var crwindu = cr[13]; //.toUpperCase();	 // UOM Wind Speed
				var cradir = cr[7];							// Wind Bearing (degrees)
// BAROMETER
				var crbaro	= (cr[10] * 1).toFixed(1); // Barometer
				$("#cajaxbaro").html(crbaro);
				var crtre = ((cr[15] == 'hPa') || (cr[15] == 'mb')) ? get_barotrendmet(cr[18]) : get_barotrendimp(cr[18]);
// UMBRELLA ICON
		 // Davis manual states "Rain Rate will show zero and the umbrella icon does not appear
		 // until two tips of the rain bucket within a 15 minute period."
		 // Min rain rate for Umbrella icon - => 1.6mm/hr or 0.08"/hr
				if (cr[8] > 0) { $("#cajaxumbr").html('<img src="' + imgdir + "umbr.png" + '" alt=""/>');
				} else { $("#cajaxumbr").html(''); }
// RAIN
				var crrainu = cr[16];							// Rain UoM
				$("#cajaxrainu").html(crrainu);
				var crrainuh = (cr[16]+'/h');					// Rain Rate UoM
				$("#cajaxrainuh").html(crrainuh);
				var dec = (crrainu != 'in') ? 1 : 2;		// Metric 'mm' 1 dec place Imp 'in' = 2 dec places
				var crrainr = (cr[8]	 * 1).toFixed(dec);	// Rain rate
				var crrainh = (cr[47] * 1).toFixed(dec);	// Rain last hour
				var crraind = (cr[9]	 * 1).toFixed(dec);	// Rain so far today
				$("#cajaxrain").html(crraind);
				var crrainm = (cr[19] * 1).toFixed(dec);	// Rain this month
				var crrainy = (cr[20] * 1).toFixed(dec);	// Rain this year
				var stormrn = (cr[78] * 1).toFixed(dec);	// Storm rain
// SOLAR ET
//				  if (showsolar == 'Y') {
				var cret = (cr[44] * 1).toFixed(dec);	// Evapotrans metric 1 dec place imp 2 dec
				var crsolar = (cr[45] * 1).toFixed(0); // Solar Radiation W/m2
//				  } // End Solar ET

// UV
				var cruv		= (cr[43] * 1).toFixed(1); // UV Index

// ICONS
				$("#cajaxicon").html('<img src="' + imgdir + fcsticon + '" alt=""/>');	// forecast icon
				$("#cajaxmoon").html('<img src="' + imgdir + moonic + '" alt=""/>');		// moonphase icon
				if (console == 'VP2') { // VP2
					if (sensorlost == 1) {			// SensorContactLost 
						var antennaon	= 'L';		// no receive symbol
						var antennaoff = 'L';		// no receive symbol
					} else {
						var antennaon	= 'X';		// antenna On
						var antennaoff = ' ';		// antenna Off
					}
				} else {						// VUE
					if (sensorlost == 1) {			// SensorContactLost 
						var antennaon	= ('<img src="' + imgdir + "antennaoff.png" + '" alt=""/>');  // antenna Off
						var antennaoff = ('<img src="' + imgdir + "antennaoff.png" + '" alt=""/>');  // antenna Off
					} else {
						var antennaon	= ('<img src="' + imgdir + "antennaon.png"  + '" alt=""/>');  // antenna On
						var antennaoff = ('<img src="' + imgdir + "antennaoff.png" + '" alt=""/>');  // antenna Off
					}
				}

// OUTPUT ROTATING DATA ITEMS ##################################################
// CONSOLE	WIND SPEED - auto rotate wind speed	 -> wind degrees on timeout interval (default 3 secs)
		 if (windrotate == 1) {
			 $("#cajaxwind").one('click').html(crwspd);
			 $("#cajaxwindu").one('click').html(crwindu);
			 $("#cajaxwinddu").one('click').html('');
		 } else if (windrotate == 2) {
		 if (windbutton == 0) { $("#cajaxwind").html(crwspd);}
		 if (windbutton == 1) { $("#cajaxwind").html(cradir);}

			 switch(windbutton)
			 {
				case 0:
				  $("#cajaxwind").one('click').html(crwspd);
				  $("#cajaxwindu").one('click').html(crwindu);
				  $("#cajaxwinddu").one('click').html('');
				  windbutton = 1;
				  break;
				case 1:
				  $("#cajaxwind").one('click').html(cradir);
				  $("#cajaxwindu").one('click').html('');
				  $("#cajaxwinddu").one('click').html('&deg;');
				  windbutton = 0;
				  break;
				default:
				  $("#cajaxwind").one('click').html(crwspd);
				  $("#cajaxwindu").one('click').html(crwindu);
				  $("#cajaxwinddu").one('click').html('');
				  windbutton = 0;
				  break;
			 }
		 }

// CONSOLE	APPARENT TEMP - auto rotate App Temp -> Dew display on timeout interval (default 3 secs)
		 if (dewrotate == 1) {
		 if (appbutton == 0) { $("#cajaxapp").html(crapp);}
		 if (appbutton == 1) { $("#cajaxapp").html(crdew);}

		switch(appbutton) {
			case 0:
				$("#cajaxapp").one('click').html(crapp);
				$('#app1').css("display", "inline-block");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").html(crapp);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 1;
				break;
			case 1:
				$("#cajaxapp").one('click').html(crdew);
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "inline-block");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").html(crdew);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 0;
				 break;
			}
		}

// CONSOLE	HUMIDEX - auto rotate Humidex -> Dew display on timeout interval (default 3 secs)
		if(dewrotate == 2) {
		if(appbutton == 0) { $("#cajaxapp").html(crdew);}
		if(appbutton == 1) { $("#cajaxapp").html(crhdex);}

		switch(appbutton) {
			case 0:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "inline-block");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crdew);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 1;
				break;
			case 1:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "inline-block");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crhdex);
				$("#ihdxuom").one('click').html('');
				appbutton = 0;
				break;
			}
		}

// CONSOLE	HEAT INDEX - auto rotate HeatIndex -> Dew display on timeout interval (default 3 secs)
		 if (dewrotate == 3) {
			if(appbutton == 0) { $("#cajaxapp").html(crheat);}
			if(appbutton == 1) { $("#cajaxapp").html(crdew);}

		 switch(appbutton) {
			case 0:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "inline-block");	//	HEAT
				$("#cajaxapp").one('click').html(crheat);
				$("#ihdxuom").one('click').html('');
				appbutton = 1;
				break;
			case 1:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "inline-block");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				 ("#cajaxapp").one('click').html(crdew);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 0;
				break;
			}
		}
// CONSOLE	APPARENT TEMP - auto rotate Dew -> App temp -> Chill -> Dew display on timeout interval (default 3 secs)
		if (dewrotate == 4) {
			if(appbutton == 0) { $("#cajaxapp").html(crapp);}
			if(appbutton == 1) { $("#cajaxapp").html(crchill);}
			if(appbutton == 2) { $("#cajaxapp").html(crdew);}

		switch(appbutton) {
			case 0:
				$('#app1').css("display", "inline-block");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crapp);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 1;
				break;
			case 1:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "inline-block");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crchill);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 2;
				break;
			case 2:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "inline-block");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crdew);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 0;
				break;
			}
		}
// CONSOLE	HUMIDEX - auto rotate Humidex -> Chill -> Dew display on timeout interval (default 3 secs)
		if (dewrotate == 5) {
			if(appbutton == 0) { $("#cajaxapp").html(crhdex);}
			if(appbutton == 1) { $("#cajaxapp").html(crchill);}
			if(appbutton == 2) { $("#cajaxapp").html(crdew);}

		switch(appbutton) {
			case 0:
			$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "inline-block");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crhdex);
				$("#itempuom").one('click').html('');
				appbutton = 1;
				break;
			case 1:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "inline-block");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crchill);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 2;
				break;
			case 2:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "inline-block");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crdew);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 0;
				break;
			}
		}
// CONSOLE	HEAT INDEX - auto rotate Heatindex -> Chill -> Dew display on timeout interval (default 3 secs)
		if (dewrotate == 6) {
			if(appbutton == 0) { $("#cajaxapp").html(crheat);}
			if(appbutton == 1) { $("#cajaxapp").html(crchill);}
			if(appbutton == 2) { $("#cajaxapp").html(crdew);}

		switch(appbutton) {
			case 0:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "inline-block");	//	HEAT
				$("#cajaxapp").one('click').html(crheat);
				$("#itempuom").one('click').html('');
				appbutton = 1;
				break;
			case 1:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "inline-block");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crchill);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 2;
				break;
			case 2:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "inline-block");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crdew);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 0;
				break;
			}
		}
// CONSOLE	APPARENT TEMP - auto rotate Dew -> App temp -> Chill -> heat -> Dew display on timeout interval (default 3 secs)
		if (dewrotate == 7) {
			if(appbutton == 0) { $("#cajaxapp").html(crapp);}
			if(appbutton == 1) { $("#cajaxapp").html(crchill);}
			if(appbutton == 2) { $("#cajaxapp").html(crheat);}
			if(appbutton == 3) { $("#cajaxapp").html(crdew);}

		switch(appbutton) {
			case 0:
				$('#app1').css("display", "inline-block");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crapp);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 1;
				break;
			case 1:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "inline-block");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crchill);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 2;
				break;
			case 2:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "inline-block");	//	HEAT
				$("#cajaxapp").one('click').html(crheat);
				$("#itempuom").one('click').html('');
				appbutton = 3;
				break;
			case 3:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "inline-block");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crdew);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 0;
				break;
			}
		}
// CONSOLE	APPARENT TEMP - auto rotate Humidex -> Chill -> heat -> dew display on timeout interval (default 3 secs)
		if (dewrotate == 8) {
			if(appbutton == 0) { $("#cajaxapp").html(crhdex);}
			if(appbutton == 1) { $("#cajaxapp").html(crchill);}
			if(appbutton == 2) { $("#cajaxapp").html(crheat);}
			if(appbutton == 3) { $("#cajaxapp").html(crdew);}

		switch(appbutton) {
			case 0:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "inline-block");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crhdex);
				$("#itempuom").one('click').html('');
				appbutton = 1;
				 break;
			case 1:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "inline-block");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				$("#cajaxapp").one('click').html(crchill);
				$("#itempuom").one('click').html(crtempu);
				appbutton = 2;
				break;
			case 2:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "none");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "inline-block");	//	HEAT
				$("#cajaxapp").one('click').html(crheat);
				$("#iheatuom").one('click').html('');
				appbutton = 3;
				break;
			case 3:
				$('#app1').css("display", "none");	//	APPARENT
				$('#app2').css("display", "inline-block");	//	DEW
				$('#app3').css("display", "none");	// HUMIDEX
				$('#app4').css("display", "none");	//	CHILL
				$('#app5').css("display", "none");	//	HEAT
				 $("#cajaxapp").one('click').html(crdew);
				 $("#itempuom").one('click').html(crtempu);
				appbutton = 0;
				 break;
			}
		} // END Temp rotates


// CONSOLE	DAILY RAIN - auto rotate Daily Rain ->Storm Rain -> ET display on timeout interval (default 3 secs)
		if (showsolar == 'N' && storm == 'Y') { // dailyrain + stormrn + solar(ET)
			if (dayrainbutton == 0) { $("#cajaxrain").html(crraind);}
			if (dayrainbutton == 1) { $("#cajaxrain").html(cret);}
			if (dayrainbutton == 2) { $("#cajaxrain").html(stormrn);}

		switch(dayrainbutton) {
			case 0:
				$('#rrd1').css("display", "inline-block");	//	DAILY RAIN
				$('#rrd2').css("display", "none");	//	ET
				$('#rrd3').css("display", "none");	//	STORM RAIN
				$("#cajaxrain").one('click').html(crraind);
				dayrainbutton = 1;
				break;
			case 1:
				$('#rrd1').css("display", "none");	//	DAILY RAIN
				$('#rrd2').css("display", "none");	//	ET
				$('#rrd3').css("display", "inline-block");	//	STORM RAIN
				$("#cajaxrain").one('click').html(stormrn);
				dayrainbutton = 0;
				break;
				} // End case
		} else if (showsolar == 'Y' && storm == 'Y' ) {
			if (dayrainbutton == 0) { $("#cajaxrain").html(crraind);}
			if (dayrainbutton == 1) { $("#cajaxrain").html(cret);}
			if (dayrainbutton == 2) { $("#cajaxrain").html(stormrn);}
		switch(dayrainbutton) {
			case 0:
				$('#rrd1').css("display", "inline-block");	//	DAILY RAIN
				$('#rrd2').css("display", "none");	//	ET
				$('#rrd3').css("display", "none");	//	STORM RAIN
				$("#cajaxrain").one('click').html(crraind);
				dayrainbutton = 1;
				break;
			case 1:
				$('#rrd1').css("display", "none");	//	DAILY RAIN
				$('#rrd2').css("display", "inline-block");	//	ET
				$('#rrd3').css("display", "none");	//	STORM RAIN
				$("#cajaxrain").one('click').html(cret);
				dayrainbutton = 2;
				break;
			case 2:
				$('#rrd1').css("display", "none");	//	DAILY RAIN
				$('#rrd2').css("display", "none");	//	ET
				$('#rrd3').css("display", "inline-block");	//	STORM RAIN
				$("#cajaxrain").one('click').html(stormrn);
				dayrainbutton = 0;
				break;
			} // End case
		} else if (showsolar == 'Y' && storm == 'N') {
			if (dayrainbutton == 0) { $("#cajaxrain").html(crraind);}
			if (dayrainbutton == 1) { $("#cajaxrain").html(cret);}
			if (dayrainbutton == 2) { $("#cajaxrain").html(stormrn);}
		switch(dayrainbutton) {
			case 0:
				$('#rrd1').css("display", "inline-block");	//	DAILY RAIN
				$('#rrd2').css("display", "none");	//	ET
				$('#rrd3').css("display", "none");	//	STORM RAIN
				$("#cajaxrain").one('click').html(crraind);
				dayrainbutton = 1;
				break;
			case 1:
				$('#rrd1').css("display", "none");	//	DAILY RAIN
				$('#rrd2').css("display", "inline-block");	//	ET
				$('#rrd3').css("display", "none");	//	STORM RAIN
				$("#cajaxrain").one('click').html(cret);
				dayrainbutton = 0;
				break;
			} // End case

		} else if (showsolar == 'N' &&  storm == 'N') {
			if (dayrainbutton == 0) { $("#cajaxrain").html(crraind);}
			if (dayrainbutton == 1) { $("#cajaxrain").html(cret);}
			if (dayrainbutton == 2) { $("#cajaxrain").html(stormrn);}
		switch(dayrainbutton) {
			case 0:
				$('#rrd1').css("display", "inline-block");	//	DAILY RAIN
				$('#rrd2').css("display", "none");	//	ET
				$('#rrd3').css("display", "none");	//	STORM RAIN
				$("#cajaxrain").one('click').html(crraind);
				dayrainbutton = 0;
				break;
			} // End case
		}


// CONSOLE	RAIN - auto rotate Rain Rate -> Rain Hour -> Rain Month -> Rain Year display on timeout interval (default 3 secs)
			 if(rainbutton == 0) { $("#cajaxrainratehr").html(crrainr);}
			 if(rainbutton == 1) { $("#cajaxrainratehr").html(crrainh);}
			 if(rainbutton == 2) { $("#cajaxrainratehr").html(crrainm);}
			 if(rainbutton == 3) { $("#cajaxrainratehr").html(crrainy);}

			switch(rainbutton) {
			case 0:
			  $('#rrh1').css("display", "inline-block");	//	RAIN RATE
			  $('#rrh2').css("display", "none");	//	RAIN HOUR
			  $('#rrh3').css("display", "none");	//	RAIN MONTH
			  $('#rrh4').css("display", "none");	//	RAIN YEAR
			  $("#cajaxrainratehr").one('click').html(crrainr);
			  $("#rrth").one('click').html(crrainu);		// uom
			  $("#rrh").css("display", "inline-block");	// /h
				rainbutton = 1;
			  break;
			case 1:
			  $('#rrh1').css("display", "none");	//	RAIN RATE
			  $('#rrh2').css("display", "inline-block");	//	RAIN HOUR
			  $('#rrh3').css("display", "none");	//	RAIN MONTH
			  $('#rrh4').css("display", "none");	//	RAIN YEAR
			  $("#cajaxrainratehr").one('click').html(crrainh);
			  $("#rrth").one('click').html(crrainu);
			  $("#rrh").css("display", "none");
			  rainbutton = 2;
			  break;
			case 2:
				$('#rrh1').css("display", "none");	//	RAIN RATE
				$('#rrh2').css("display", "none");	//	RAIN HOUR
				$('#rrh3').css("display", "inline-block");	//	RAIN MONTH
				$('#rrh4').css("display", "none");	//	RAIN YEAR
				$("#cajaxrainratehr").one('click').html(crrainm);
				$("#rrth").one('click').html(crrainu);
				$("#rrh").css("display", "none");
				rainbutton = 3;
				break;
			case 3:
				$('#rrh1').css("display", "none");	//	RAIN RATE
				$('#rrh2').css("display", "none");	//	RAIN HOUR
				$('#rrh3').css("display", "none");	//	RAIN MONTH
				$('#rrh4').css("display", "inline-block");	//	RAIN YEAR
				$("#cajaxrainratehr").one('click').html(crrainy);
				$("#rrth").one('click').html(crrainu);
				$("#rrh").css("display", "none");
				rainbutton = 0;
				break;
			}

// CONSOLE	TEMP IN / SOLAR - auto rotate display on timeout interval (default 3 secs)
		if (showsolar == 'Y') {
			if(tempinbutton == 0) { $("#cajaxitemp").html(critemp);}
			if(tempinbutton == 1) { $("#cajaxitemp").html(crsolar);}

		switch(tempinbutton) {
			case 0:
				$('#itemp1').css("display", "inline-block");	//	TEMP IN
				$('#itemp2').css("display", "none");	//	SOL W/m2
				$("#cajaxitemp").one('click').html(critemp);
				$("#itempinuom").one('click').html(crtempu);
				tempinbutton = 1;
				break;
			case 1:
				$('#itemp1').css("display", "none");	//	TEMP IN
				$('#itemp2').css("display", "inline-block");	//	SOL W/m2
				$("#cajaxitemp").one('click').html(crsolar);
				$("#itempinuom").one('click').html('');
				tempinbutton = 0;
				break;
			} // end switch
		} else {
				$('#itemp1').css("display", "inline-block");	//	TEMP IN
				$('#itemp2').css("display", "none");	//	SOL W/m2
				$("#cajaxitemp").one('click').html(critemp);
				$("#itempinuom").one('click').html(crtempu);
		} // End showsolar

// CONSOLE	UV -> HUM IN - auto rotate display on timeout interval (default 3 secs)
		if (showuv == 'Y') {
			if(huminbutton == 0) { $("#cajaxihumidity").html(crihum);}
			if(huminbutton == 1) { $("#cajaxihumidity").html(cruv);}

		switch(huminbutton) {
			case 0:
				$('#ihum1').css("display", "inline-block");	//	HUM IN
				$('#ihum2').css("display", "none");	//	UV
				$("#cajaxihumidity").one('click').html(crihum);
				$("#ihumuom").one('click').html('%');
				$("#index").one('click').html('');
				huminbutton = 1;
				break;
			case 1:
				$('#ihum1').css("display", "none");	//	HUM IN
				$('#ihum2').css("display", "inline-block");	//	UV
				$("#cajaxihumidity").one('click').html(cruv);
				$("#ihumuom").one('click').html('');
				$("#index").one('click').html('index');
				huminbutton = 0;
				 break;
		} // end switch
		} else {
				$('#ihum1').css("display", "inline-block");	//	HUM IN
				$('#ihum2').css("display", "none");	//	UV
				$("#cajaxihumidity").one('click').html(crihum);
				$("#ihumuom").one('click').html('%');
				$("#index").one('click').html('');
		} // End HUM IN -> UV

// ANTENNA ICON - SWITCH ON/OFF at timeout interval (default 3 secs)
		switch(antennaicon) {
			case 0:
				$("#cajaxanten").one('click').html(antennaon);
				antennaicon = 1;
				break;
			case 1:
				$("#cajaxanten").one('click').html(antennaoff);
				antennaicon = 0;
				break;
				default:
					$("#cajaxanten").one('click').html(antennaon);
					antennaicon = 0;
					break;
		} // end switch
		  
// Arrows
			// Wind
			$("#windarrow").css("transform", "rotate("+(cradir*1 +90)+"deg");
			// Baro trend
			var barotrend = $("#cajaxbaroarrow");
			barotrend.css("transform", "rotate("+(crtre-90)+"deg");

// Countdown timers
			if (lastajaxtimeformatdc != timeonly) {
				counterSecdcup = 0;								// reset timer - not being used
				counterSecdcdn = 0;								// reset timer
				lastajaxtimeformatdc = timeonly;	 // remember this time
			}

		} // End onDataReceived(series) function

// Countdown timers
$.get(dataurl, {"ts": Date.now()}, function(series) {
	onDataReceived(series);
});

// itimeout
		setTimeout(update, itimeout*1000);

// Baro trend arrow angle metric
		function get_barotrendmet(btrnd) {
			if (btrnd >	 0.8) { return('16'); }
			if (btrnd >	 0.5) { return('30'); }
			if (btrnd >	 0.3) { return('45'); }
			if (btrnd >	 0.15) { return('60'); }
			if (btrnd >	 0.05) { return('75'); }
			if ((btrnd >= -0.05) && (btrnd <= 0.05)) { return('90'); }
			if (btrnd < -0.8) { return('164'); }
			if (btrnd < -0.5) { return('150'); }
			if (btrnd < -0.3) { return('135'); }
			if (btrnd < -0.15) { return('120'); }
			if (btrnd < -0.05) { return('105'); }
			return(btrnd);
		}
// Baro trend arrow angle imperial
		function get_barotrendimp(btrnd) {
			if (btrnd >	 0.04) { return('16'); }
			if (btrnd >	 0.025) { return('30'); }
			if (btrnd >	 0.015) { return('45'); }
			if (btrnd >	 0.0075) { return('60'); }
			if (btrnd >	 0.0025) { return('75'); }
			if ((btrnd >= -0.0025) && (btrnd <= 0.0025)) { return('90'); }
			if (btrnd < -0.04) { return('164'); }
			if (btrnd < -0.025) { return('150'); }
			if (btrnd < -0.015) { return('135'); }
			if (btrnd < -0.0075) { return('120'); }
			if (btrnd < -0.0025) { return('105'); }
			return(btrnd);
		}

	}; // End Function update

}); // End Function


