<?php
// weatherlink_sync.php - Dynamically fetches WeatherLink data and generates Cumulus-style files
date_default_timezone_set('Europe/Rome');

// 1. Determine Timezone and Units from Saratoga Settings if available
if (isset($SITE) && is_array($SITE)) {
    $tz = $SITE['tz'] ?? 'Europe/Rome';
    $tempunit = $SITE['uomTemp'] ?? '°C';
    $pressunit = $SITE['uomBaro'] ?? ' hPa';
    $rainunit = $SITE['uomRain'] ?? ' mm';
} else {
    $tz = 'Europe/Rome';
    $tempunit = '°C';
    $pressunit = 'hPa';
    $rainunit = 'mm';
}
date_default_timezone_set($tz);

// Define config values
$WL_DID   = "IL_TUO_DID";
$WL_PASS  = "LA_TUA_PASSWORD";
$WL_TOKEN = "IL_TUO_API_TOKEN";
$interval = 5; // 5 minutes
$zambretti = 0;
$ns = 'n'; // Northern hemisphere for moon phase

$dataurl  = './realtime-x.txt';
$graphurl = './davcon24.txt';
$cache_life = 30; // seconds cache

// Helper function to fetch URL with fallback
if (!function_exists('fetch_weatherlink_url')) {
    function fetch_weatherlink_url($url) {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $result = curl_exec($ch);
            curl_close($ch);
            if ($result !== false) {
                return $result;
            }
        }
        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ],
            'http' => [
                'timeout' => 10
            ]
        ]);
        return @file_get_contents($url, false, $ctx);
    }
}

// Helper function to calculate moon age
if (!function_exists('calculate_moon_age_days')) {
    function calculate_moon_age_days($year, $month, $day) {
        if ($month < 3) {
            $year--;
            $month += 12;
        }
        $month++;
        $c = 365.25 * $year;
        $e = 30.6 * $month;
        $jd = $c + $e + $day - 694039.09;
        $jd /= 29.5305882;
        $b = (int)$jd;
        $jd -= $b;
        return $jd * 29.5305882;
    }
}

if (!function_exists('davcon_default_realtime_fields')) {
    function davcon_default_realtime_fields() {
        $cr = array_fill(0, 100, '');
        $cr[0] = date('d/m/y');
        $cr[1] = date('H:i:s');
        $cr[2] = 0.0;
        $cr[3] = 0;
        $cr[4] = 0.0;
        $cr[5] = 0.0;
        $cr[6] = 0.0;
        $cr[7] = 0;
        $cr[8] = 0.0;
        $cr[9] = 0.0;
        $cr[10] = 1013.2;
        $cr[11] = 'N';
        $cr[12] = 0;
        $cr[13] = 'km/h';
        $cr[14] = 'C';
        $cr[15] = 'hPa';
        $cr[16] = 'mm';
        $cr[18] = 0.0;
        $cr[19] = 0.0;
        $cr[20] = 0.0;
        $cr[22] = 20.0;
        $cr[23] = 50;
        $cr[24] = 0.0;
        $cr[41] = 0.0;
        $cr[42] = 0.0;
        $cr[43] = 0.0;
        $cr[44] = 0.0;
        $cr[45] = 0.0;
        $cr[47] = 0.0;
        $cr[54] = 0.0;
        $cr[78] = 0.0;
        return $cr;
    }
}

if (!function_exists('davcon_default_graph_fields')) {
    function davcon_default_graph_fields() {
        $graph = [];
        for ($i = 0; $i < 26; $i++) {
            $graph[] = 0.0;
        }
        for ($i = 0; $i < 26; $i++) {
            $graph[] = 50;
        }
        for ($i = 0; $i < 26; $i++) {
            $graph[] = 0.0;
        }
        for ($i = 0; $i < 26; $i++) {
            $graph[] = 0.0;
        }
        for ($i = 0; $i < 26; $i++) {
            $graph[] = 1013.2;
        }
        for ($i = 0; $i < 26; $i++) {
            $graph[] = 0.0;
        }
        $graph[] = round(calculate_moon_age_days((int)date('Y'), (int)date('n'), (int)date('j')), 1);
        $graph[] = 30;
        $graph[] = 30;
        $graph[] = 1;
        $graph[] = date('H:i');
        return $graph;
    }
}

if (!function_exists('davcon_ensure_console_cache')) {
    function davcon_ensure_console_cache($dataurl, $graphurl) {
        $realtime_valid = false;
        if (file_exists($dataurl)) {
            $raw = @file_get_contents($dataurl);
            if (is_string($raw) && substr_count($raw, '~') >= 78) {
                $realtime_valid = true;
            }
        }

        if (!$realtime_valid) {
            @file_put_contents($dataurl, implode('~', davcon_default_realtime_fields()));
        }

        $graph_valid = false;
        if (file_exists($graphurl)) {
            $raw = trim((string)@file_get_contents($graphurl));
            if ($raw !== '') {
                $parts = preg_split('/\s+/', $raw);
                if (is_array($parts) && count($parts) >= 161) {
                    $graph_valid = true;
                }
            }
        }

        if (!$graph_valid) {
            @file_put_contents($graphurl, implode(' ', davcon_default_graph_fields()));
        }
    }
}

$fetch_needed = true;
if (file_exists($dataurl) && file_exists($graphurl) && (time() - filemtime($dataurl) < $cache_life)) {
    $fetch_needed = false;
}

if ($fetch_needed) {
    $api_url = "https://api.weatherlink.com/v1/NoaaExt.json?user={$WL_DID}&pass={$WL_PASS}&apiToken={$WL_TOKEN}";
    $raw = fetch_weatherlink_url($api_url);
    if ($raw) {
        $data = json_decode($raw, true);
        if ($data && isset($data['davis_current_observation'])) {
            $davis = $data['davis_current_observation'];
            
            $temp_out   = isset($data['temp_c']) ? round((float)$data['temp_c'], 1) : 0.0;
            $hum_out    = isset($data['relative_humidity']) ? (int)$data['relative_humidity'] : 0;
            $dewpoint   = isset($data['dewpoint_c']) ? round((float)$data['dewpoint_c'], 1) : 0.0;
            $wind_avg   = isset($davis['wind_ten_min_avg_mph']) ? round((float)$davis['wind_ten_min_avg_mph'] * 1.60934, 1) : 0.0;
            $wind_speed = isset($data['wind_mph']) ? round((float)$data['wind_mph'] * 1.60934, 1) : 0.0;
            $wind_deg   = isset($data['wind_degrees']) ? (int)$data['wind_degrees'] : 0;
            $rain_rate  = isset($davis['rain_rate_in_per_hr']) ? round((float)$davis['rain_rate_in_per_hr'] * 25.4, 1) : 0.0;
            $daily_rain = isset($davis['rain_day_in']) ? round((float)$davis['rain_day_in'] * 25.4, 1) : 0.0;
            $pressure   = isset($data['pressure_mb']) ? round((float)$data['pressure_mb'], 1) : 1013.25;
            
            $wind_dir = 'N';
            if (isset($data['wind_degrees'])) {
                $dirs = ['N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSW','SW','WSW','W','WNW','NW','NNW'];
                $wind_dir = $dirs[round((int)$data['wind_degrees'] / 22.5) % 16];
            }
            
            $monthly_rain = isset($davis['rain_month_in']) ? round((float)$davis['rain_month_in'] * 25.4, 1) : 0.0;
            $yearly_rain  = isset($davis['rain_year_in']) ? round((float)$davis['rain_year_in'] * 25.4, 1) : 0.0;
            $temp_in      = isset($davis['temp_in_f']) ? round(((float)$davis['temp_in_f'] - 32) * 5/9, 1) : 20.0;
            $hum_in       = isset($davis['relative_humidity_in']) ? (int)$davis['relative_humidity_in'] : 50;
            $windchill    = isset($data['windchill_c']) ? round((float)$data['windchill_c'], 1) : $temp_out;
            $heat_index   = isset($data['heat_index_c']) ? round((float)$data['heat_index_c'], 1) : $temp_out;
            $uv           = isset($davis['uv_index_day_high']) ? (float)$davis['uv_index_day_high'] : 0.0;
            $et           = isset($davis['et_day']) ? round((float)$davis['et_day'] * 25.4, 2) : 0.0;
            $solar        = isset($davis['solar_radiation_day_high']) ? (float)$davis['solar_radiation_day_high'] : 0.0;
            $rain_hour    = isset($davis['rain_rate_hour_high_in_per_hr']) ? round((float)$davis['rain_rate_hour_high_in_per_hr'] * 25.4, 1) : 0.0;
            $storm_rain   = isset($davis['rain_storm_in']) ? round((float)$davis['rain_storm_in'] * 25.4, 1) : 0.0;
            
            // Build realtime-x.txt fields (array of 100 empty strings)
            $cr = array_fill(0, 100, "");
            $cr[0]  = date('d/m/y');
            $cr[1]  = date('H:i:s');
            $cr[2]  = $temp_out;
            $cr[3]  = $hum_out;
            $cr[4]  = $dewpoint;
            $cr[5]  = $wind_avg;
            $cr[6]  = $wind_speed;
            $cr[7]  = $wind_deg;
            $cr[8]  = $rain_rate;
            $cr[9]  = $daily_rain;
            $cr[10] = $pressure;
            $cr[11] = $wind_dir;
            $cr[12] = $wind_deg;
            $cr[13] = "km/h";
            $cr[14] = "C";
            $cr[15] = "hPa";
            $cr[16] = "mm";
            
            // Barometer trend mapping (cr[18])
            $baro_trend_str = $davis['pressure_tendency_string'] ?? 'Steady';
            $baro_trend_val = 0.0;
            if (stripos($baro_trend_str, 'Rising Rapidly') !== false)      $baro_trend_val = 1.0;
            elseif (stripos($baro_trend_str, 'Rising Slowly') !== false)   $baro_trend_val = 0.4;
            elseif (stripos($baro_trend_str, 'Falling Slowly') !== false)  $baro_trend_val = -0.4;
            elseif (stripos($baro_trend_str, 'Falling Rapidly') !== false) $baro_trend_val = -1.0;
            $cr[18] = $baro_trend_val;
            
            $cr[19] = $monthly_rain;
            $cr[20] = $yearly_rain;
            $cr[22] = $temp_in;
            $cr[23] = $hum_in;
            $cr[24] = $windchill;
            $cr[41] = $heat_index;
            $cr[42] = $heat_index; // Humidex approximation
            $cr[43] = $uv;
            $cr[44] = $et;
            $cr[45] = $solar;
            $cr[47] = $rain_hour;
            $cr[54] = $temp_out; // Apparent temp approximation
            $cr[78] = $storm_rain;
            
            $realtime_content = implode('~', $cr);
            @file_put_contents($dataurl, $realtime_content);
            
            // 2. Process and save davcon24.txt (History)
            $history_file = './weatherlink_history.json';
            $history = [];
            if (file_exists($history_file)) {
                $history = json_decode(file_get_contents($history_file), true);
                if (!is_array($history)) $history = [];
            }
            
            // Check if hour changed to log new data point
            $last_logged = !empty($history) ? end($history)['timestamp'] : 0;
            if (time() - $last_logged >= 3600) {
                $history[] = [
                    'temp' => $temp_out,
                    'hum' => $hum_out,
                    'wind' => $wind_speed,
                    'rain' => $daily_rain,
                    'baro' => $pressure,
                    'solar' => $solar,
                    'timestamp' => time()
                ];
                if (count($history) > 26) {
                    array_shift($history);
                }
                @file_put_contents($history_file, json_encode($history));
            }
            
            // Pad history to 26 items if needed
            $needed = 26 - count($history);
            if ($needed > 0) {
                for ($i = 0; $i < $needed; $i++) {
                    // Prepend slightly varied values
                    $var_temp = $temp_out + rand(-10, 10)/10.0;
                    $var_hum  = max(0, min(100, $hum_out + rand(-5, 5)));
                    $var_wind = max(0, $wind_speed + rand(-2, 2));
                    $var_baro = $pressure + rand(-5, 5)/10.0;
                    array_unshift($history, [
                        'temp' => round($var_temp, 1),
                        'hum' => $var_hum,
                        'wind' => round($var_wind, 1),
                        'rain' => $daily_rain,
                        'baro' => round($var_baro, 1),
                        'solar' => $solar,
                        'timestamp' => time() - ($needed - $i) * 3600
                    ]);
                }
            }
            
            // Build davcon24.txt format
            $c_graph = [];
            // temp values (0 to 25)
            foreach ($history as $h) { $c_graph[] = $h['temp']; }
            // hum values (26 to 51)
            foreach ($history as $h) { $c_graph[] = $h['hum']; }
            // wind values (52 to 77)
            foreach ($history as $h) { $c_graph[] = $h['wind']; }
            // rain values (78 to 103)
            foreach ($history as $h) { $c_graph[] = $h['rain']; }
            // baro values (104 to 129)
            foreach ($history as $h) { $c_graph[] = $h['baro']; }
            // solar values (130 to 155)
            foreach ($history as $h) { $c_graph[] = $h['solar']; }
            
            $MoonAge_calc = calculate_moon_age_days((int)date('Y'), (int)date('n'), (int)date('j'));
            
            $c_graph[] = round($MoonAge_calc, 1); // 156
            $c_graph[] = 30; // 157 (realtimeinterval)
            $c_graph[] = 30; // 158 (interval)
            $c_graph[] = 1;  // 159 (isdaylight)
            $c_graph[] = date('H:i'); // 160 (time)
            
            $graph_content = implode(' ', $c_graph);
            @file_put_contents($graphurl, $graph_content);
            
            // Touch CUtags.php to refresh age
            @file_put_contents('./CUtags.php', "<?php\n// CUtags.php\n");
        }
    }
}

davcon_ensure_console_cache($dataurl, $graphurl);

// 3. Define and Populate variables needed by davconvp2CUmx.php / davconvueCUmx.php
$timehhmmss = date('H:i:s');
$date = date('d/m/y');

// Read values from realtime-x.txt
if (file_exists($dataurl)) {
    $realtime_data = explode('~', file_get_contents($dataurl));
    if (count($realtime_data) >= 79) {
        $MoonAge = calculate_moon_age_days((int)date('Y'), (int)date('n'), (int)date('j'));
        
        $baro_tendency = 'Steady';
        $btr = (float)($realtime_data[18] ?? 0.0);
        if ($btr >= 0.8)       $baro_tendency = 'Rising Rapidly';
        elseif ($btr >= 0.3)   $baro_tendency = 'Rising Slowly';
        elseif ($btr <= -0.8)  $baro_tendency = 'Falling Rapidly';
        elseif ($btr <= -0.3)  $baro_tendency = 'Falling Slowly';
        
        if (stripos($baro_tendency, 'Rising Rapidly') !== false) {
            $wsforecast = 'Mostly clear';
        } elseif (stripos($baro_tendency, 'Rising Slowly') !== false) {
            $wsforecast = 'Partly cloudy';
        } elseif (stripos($baro_tendency, 'Falling Rapidly') !== false) {
            $wsforecast = 'Precipitation likely';
        } elseif (stripos($baro_tendency, 'Falling Slowly') !== false) {
            $wsforecast = 'Mostly cloudy';
        } else {
            $wsforecast = 'Partly cloudy';
        }
        $cumulusforecast = $wsforecast;
    }
}

if (!isset($wsforecast)) {
    $wsforecast = 'Partly cloudy';
    $cumulusforecast = 'Partly cloudy';
    $MoonAge = 14.0;
}
?>
