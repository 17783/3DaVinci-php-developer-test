<?php

function firstRun() {
	
	$host    = '127.0.0.1';
	$db      = '3davinci';
	$user    = 'root';
	$pass    = '';
	$charset = 'utf8';
	
	$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
	$dsndb = "mysql:host=$host;charset=$charset";
	$options = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	try {
		$pdoDB = new PDO($dsndb, $user, $pass, $options);
	}
	catch (\PDOException $e) {
		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}
	
	$dbSQL = "CREATE DATABASE IF NOT EXISTS " . $db;
	$stmt = $pdoDB->prepare($dbSQL);
	$stmt->execute();
	
	try {
		$pdo = new PDO($dsn, $user, $pass, $options);
	}
	catch (\PDOException $e) {
		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}
	
	$sql = "CREATE TABLE IF NOT EXISTS `user` (
        `github_id` int(11) UNSIGNED NOT NULL,
        `github_login` varchar(255) NOT NULL,
        PRIMARY KEY (github_id)
		) ENGINE=InnoDB;";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
}

firstRun();