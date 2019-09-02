<?php
function getAllUsers() {
	$config   = require('config.php');
	$since    = 54820500;
	$last_id  = 54820501;
	$per_page = 100;
	// create curl resource
//	while ($last_id > $since) {
do {
		$since   = $last_id;
		$ch      = curl_init();
		$fullUrl = "https://api.github.com/users?per_page=" . $per_page . "&since=" . $since;
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		// set url
		curl_setopt($ch, CURLOPT_URL, $fullUrl);
		
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		// $output contains the output string
		$output = curl_exec($ch);
		
		// close curl resource to free up system resources
		curl_close($ch);
		//parse json string to an ass array
		$convertedOutput = json_decode($output, true);
		$last_id         = end($convertedOutput)['id'];
	} while ($last_id > $since);
//	var_dump($convertedOutput);
	echo "\n" . $last_id;
	return $convertedOutput;
}

getAllUsers();