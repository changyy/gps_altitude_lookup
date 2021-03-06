<?php
$gapi = 'https://maps.googleapis.com/maps/api/elevation/json';

$pwd = dirname(__FILE__);
$default_config = $pwd."/config.php";
if (file_exists($default_config))
	require $default_config;
// $google_api_key = 'Google API Key';
if (!isset($google_api_key)) {
	echo "[ERROR] google_api_key is not defined\n";
	exit;
}

if (!isset($record_dir))
	$record_dir = $pwd.'/record';
if (!isset($query_dir))
	$query_dir = $pwd.'/google_query';

if (!file_exists($record_dir)) {
	echo "[ERROR] record_dir is not defined\n";
	exit;
}

if (!file_exists($query_dir)) {
	echo "[ERROR] query_dir is not defined\n";
	exit;
}

$record_list = array();
if (is_dir($record_dir) && (($dh = opendir($record_dir)))) {
	while (false !== ($filename = readdir($dh))) {
		if ($filename == '.' || $filename == '..')
			continue;
		if (!file_exists($query_dir.'/'.$filename))
			array_push($record_list, $filename);
	}
}
if (!file_exists($query_dir) && !mkdir($query_dir)) {
	echo "ERROR at create dir: [$query_dir]\n";
	exit;
}
foreach($record_list as $log) {
	$in_path = $record_dir.'/'.$log;
	$out_path = $query_dir.'/'.$log;
	$out_url_path = $query_dir.'/'.$log.'-url';

	echo "[INFO] Query: $in_path\n";

	$raw_gps = explode("\n", file_get_contents($in_path));
	$gps = array();
	foreach($raw_gps as $line) {
		if (strpos($line,","))
			array_push($gps, $line);
	}
	date_default_timezone_set('asia/taipei');
	do{ 
		echo date('Y-m-d H:i:s')."\n";sleep(1);
		$query_points = array_splice($gps, 0, 50);
		if (count($query_points) == 0)
			break;
		/*
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $gapi);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
			'key' => $google_api_key,
			'locations' => implode('|', $query_points)
		)));
		$result = curl_exec($ch);
		curl_close($ch);
		file_put_contents($out_path, $result);
		*/
		$query_url = $gapi."?".http_build_query(array(
			'key' => $google_api_key,
			'locations' => implode('|', $query_points)
		));
		file_put_contents($out_url_path, $query_url."\n", FILE_APPEND);
		file_put_contents($out_path, file_get_contents($query_url), FILE_APPEND);
	} while(count($gps) > 0);
}
