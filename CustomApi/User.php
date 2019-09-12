<?php
namespace CustomApi;
use Github;

class MyUser extends Github\Api\User {
	public function all($id = null, $perPage=10)
	{
		if (!is_int($id)) {
			return $this->get('/users');
		}
		return $this->get('/users', ['since' => rawurldecode($id), 'per_page' => $perPage]);
	}
}