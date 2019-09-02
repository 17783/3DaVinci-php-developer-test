<?php

function firstRun() {
	$config  = require('config.php');
	$host    = $config['host'];
	$db      = $config['db'];
	$user    = $config['user'];
	$pass    = $config['pass'];
	$charset = $config['charset'];
	
	//create the db if it isn't
	$dsn     = "mysql:host=$host;charset=$charset";
	$options = $config['options'];
	try {
		$pdo = new PDO($dsn, $user, $pass, $options);
	}
	catch (\PDOException $e) {
		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}
	
	$dbSQL = "CREATE DATABASE IF NOT EXISTS " . $db;
	$stmt  = $pdo->prepare($dbSQL);
	$stmt->execute();
	
	//now let's create the table
	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	
	try {
		$pdo = new PDO($dsn, $user, $pass, $options);
	}
	catch (\PDOException $e) {
		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}
	
	$sql  = "CREATE TABLE IF NOT EXISTS `user` (
        `github_id` int(11) UNSIGNED NOT NULL,
        `github_login` varchar(255) NOT NULL,
        PRIMARY KEY (github_id)
		) ENGINE=InnoDB;";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
}

firstRun();