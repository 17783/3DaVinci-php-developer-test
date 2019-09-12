<?php

require_once __DIR__ . '/vendor/autoload.php';

class MainClass {
	private static $_db;
	
	// get config from config.php
	private static function getConfig(): array {
		return require('config.php');
	}
	
	private static function db() {
		if (self::$_db) {
			return self::$_db;
		}
		else {
			// get connection options from config
			$config = self::getConfig();
			
			$host    = $config['host'];
			$db      = $config['db'];
			$user    = $config['user'];
			$pass    = $config['pass'];
			$charset = $config['charset'];
			$dsn     = "mysql:host={$host};dbname={$db};charset={$charset}";
			$options = $config['options'];
			
			try {
				self::$_db = new PDO($dsn, $user, $pass, $options);
			}
			catch (\PDOException $e) {
				throw new \PDOException($e->getMessage(), (int)$e->getCode());
			}
			
			return self::$_db;
		}
		
	}
	
	private static function getUser($username) {
		// TODO, if per-user search is needed
	}
	
	// API stuff
	
	/// new getUsers variant via knplabs' composer gihub api instead of curl
	private static function getUsers2($since, $per_page): array {
		
		$client = new CustomApi\MyClient();
		
		$users = $client->api('user')->all((int)$since, $per_page);

//		$users = $client->api('user')->all(13650163);
		
		return $users;
	}
	
	private static function writeToDb($users) {
		self::db()->beginTransaction();
		
		$stmt = self::db()->prepare("INSERT INTO user (github_id, github_login) VALUES(?, ?) ON DUPLICATE KEY UPDATE github_login = ?");
		
		foreach ($users as $user) {
			$data = [
				$user['id'],
				$user['login'],
				$user['login'],
			];
			
			$stmt->execute($data);
			echo '* added user ' . $user['login'] . ' with id ' . $user['id'] . "\n";
		}
		
		self::db()->commit();
	}
	
	public static function start($since, $per_page) {
		
		// 1) get users via API
		$users = self::getUsers2($since, $per_page);
		
		// 2) write results into DB
		self::writeToDb($users);
	}
}

//TODO: add users search since the last/latest/biggest id stored in DB

MainClass::start($argv[1], $argv[2]);