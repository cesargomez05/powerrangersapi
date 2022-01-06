<?php

namespace App\Entities;

class User extends APIEntity
{
	protected $resource = 'users';

	public function getPassword()
	{
		return "********";
	}
}
