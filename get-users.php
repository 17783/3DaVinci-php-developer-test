<?php

/**
 * Этот класс будет "главным".
 */
class MainClass {
	/**
	 * Тут у нас будет синглтон подключения к БД
	 *
	 */
	private static $_db;
	// Что нам вообще надо?
	// 1. Прочитать конфиг и подключиться к БД
	// 2. Сделать запрос к API и записать данные в БД
	
	/**
	 * Возвращает конфиг.
	 * Подразумеваем, что конфиг находится в файле config.php
	 */
	private static function getConfig(): array {
		return require('config.php');
	}
	
	/**
	 * Возвращает подключение к БД
	 */
	private static function db() {
		// Если подключение уже создано - просто возвращаем его
		if (self::$_db) {
			// Смысл в том, что мы сразу возвращаем подключение, если оно уже раньше было создано (т.е. если эту функцию уже вызывали до этого)
			return self::$_db;
		}
		else {
			// Если функция вызывается первый раз, то self::$_db будет пустым - надо создать подключение и записать его туда.
			
			// Формируем опции подключения
			$config = self::getConfig();
			
			$host    = $config['host'];
			$db      = $config['db'];
			$user    = $config['user'];
			$pass    = $config['pass'];
			$charset = $config['charset'];
			$dsn     = "mysql:host={$host};dbname={$db};charset={$charset}";
			$options = $config['options'];
			
			// Само подключение
			try {
				self::$_db = new PDO($dsn, $user, $pass, $options);
				// вот ^ это НЕ понятно
			}
			catch (\PDOException $e) {
				// Немного избыточно это, но пока не буду трогать.
				// так было в копипасте, я хз
				throw new \PDOException($e->getMessage(), (int)$e->getCode());
			}
			
			// Возвращаем подключение
			return self::$_db;
		}
		
	}
	
	private static function getUser($username) {
		// TODO, если вообще надо
	}
	
	/**
	 * Делает запрос к API
	 */
	private static function getUsers(): array {
		// TODO: магия с CURL
		// create curl resource
		$ch = curl_init();
		
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
	}
	

	private static function start() {
		// Что нам надо:
		
		// 1) сделать запрос к API по некоему URL
		$users = self::getUsers();
		
		// 2) записать результат в БД
		self::writeToDb($users);
	}
}