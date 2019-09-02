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

function getUsers($since = 0, $per_page = 10) {
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
//	foreach ($convertedOutput as $user) {echo $user['login'] . "\n";}
//	$lastId = end($convertedOutput)['id'];
//	echo $lastId;
	
	return $convertedOutput;
}

function checkDBUsr($uid, $login) {
	$host    = '127.0.0.1';
	$db      = '3davinci';
	$user    = 'root';
	$pass    = '';
	$charset = 'utf8';
	
	$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
	$options = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	try {
		$pdo = new PDO($dsn, $user, $pass, $options);
	}
	catch (\PDOException $e) {
		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}
	
	$stmt = $pdo->query('SELECT * FROM user WHERE github_id=' . $uid);
	$row  = $stmt->fetch();
	if ($row['github_id'] == $uid) {
//		echo "userID " . $uid . " exists";
		if ($row['github_login'] == $login) {
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
//	var_dump($userIds);
}

function dbOPS($payload, $operation) {
	$host    = '127.0.0.1';
	$db      = '3davinci';
	$user    = 'root';
	$pass    = '';
	$charset = 'utf8';
	
	$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
	$options = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	try {
		$pdo = new PDO($dsn, $user, $pass, $options);
	}
	catch (\PDOException $e) {
		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}

	$data     = [
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
	}
	else {
		echo "unknown operation";
	}
	
}


foreach (getUsers() as $user) {
	if (!checkDBUsr($user["id"], $user["login"])) {
		dbOps($user, "insert");
		echo "added user " . $user["login"] . " with id=" . $user["id"] . "\n";
	}
	else if (checkDBUsr($user["id"], $user["login"]) == 2) {
		echo "userID " . $user['id'] . " exists, " . "login change required" . "\n";
		dbOps($user, "update");
	}
	else {
		echo "userID " . $user['id'] . " exists, " . "user login match, no changes required" . "\n";
	}
}

?>