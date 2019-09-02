<?php

function getUser($username = "octocat") {
	// create curl resource
	
	$ch      = curl_init();
	$fullUrl = "https://api.github.com/users/" . $username;
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
	echo "got user: " . $convertedOutput['login'];
	
	return $convertedOutput;
}

function getUsers() {
	$config = require('config.php');
	$since = $config['since'];
	$per_page = $config['per_page'];
	// create curl resource
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
	
	return $convertedOutput;
}

function dbOPS($payload, $operation) {
	$config = require('config.php');
	$host    = $config['host'];
	$db      = $config['db'];
	$user    = $config['user'];
	$pass    = $config['pass'];
	$charset = $config['charset'];
	$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
	$options = $config['options'];
	try {
		$pdo = new PDO($dsn, $user, $pass, $options);
	}
	catch (\PDOException $e) {
		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}
	
	$data = [
		'github_id'    => $payload['id'],
		'github_login' => $payload['login'],
	];
	if ($operation == 'insert') {
		$sql  = "INSERT INTO user (github_id, github_login) VALUES (:github_id, :github_login)";
		$stmt = $pdo->prepare($sql);
		$stmt->execute($data);
	}
	else if ($operation == 'update') {
		$sql  = "UPDATE user SET github_login=:github_login WHERE github_id=:github_id";
		$stmt = $pdo->prepare($sql);
		$stmt->execute($data);
	} elseif ($operation == 'check'){
		$stmt = $pdo->query('SELECT * FROM user WHERE github_id=' . $payload['id']);
		$row  = $stmt->fetch();
		if ($row['github_id'] == $payload['id']) {
//		echo "userID " . $uid . " exists";
			if ($row['github_login'] == $payload['login']) {
				return 1;
				// user exists, no changes required
			}
			else {
				return 2;
				// user exists, login update required
			}
			
		}
		else {
//		echo "user not found in database";
			return 0;
			//no user with given id found in the db
		}
	}
	else {
		echo "unknown operation";
	}
	
}

foreach (getUsers() as $user) {
	if (!dbOps($user, 'check')) {
		dbOps($user, "insert");
		echo "added user " . $user["login"] . " with id=" . $user["id"] . "\n";
	}
	else if (dbOps($user, 'check') == 2) {
		echo "userID " . $user['id'] . " exists, " . "login changed" . "\n";
		dbOps($user, "update");
	}
	else {
		echo "userID " . $user['id'] . " exists, " . "user login match, no changes required" . "\n";
	}
}

?>