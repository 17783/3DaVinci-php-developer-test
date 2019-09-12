<?php
namespace CustomApi;
use Github;
use Github\Exception\InvalidArgumentException;

class MyClient extends Github\Client {
	public function api($name)
	{
		switch ($name) {
			
			case 'user':
			case 'users':
				$api = new MyUser($this);
				break;
			
			default:
				throw new InvalidArgumentException(sprintf('Undefined api instance called: "%s"', $name));
		}
		
		return $api;
	}
	
}