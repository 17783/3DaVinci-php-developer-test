<?php

return [
	'since'    => '54822181', // starting user id
	'per_page' => '100', //results per request
	'host'     => '127.0.0.1',
	'db'       => 'mydb',
	'user'     => 'root',
	'pass'     => '',
	'charset'  => 'utf8',
	
	'options' => [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	],
];