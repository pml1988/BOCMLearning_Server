<?php

namespace Bouncer;

use Response, View;

class Bouncer
{
	protected $_eyes = null;

	public function is_api_call($uri)
	{
		$parts = explode('/', $uri);

		foreach(Rules::api_calls() as $c) {
			if(in_array($c, $parts))
				return true;
		}

		return false;
	}

	public static function investigate($user)
	{
		return new static(Eyes::on($user));
	}

	public static function is_gaurded($uri)
	{
		foreach(Rules::all_paths() as $p)
		{
			if(starts_with($uri, $p))
				return true;
		}

		return false;
	}

	public function __construct(Eyes $eyes)
	{
		$this->_eyes = $eyes;
	}

	public function eyes()
	{
		return $this->_eyes;
	}

	public function check_access_on($uri)
	{
		return $this->_eyes->is_allowed_on($uri);
	}

	public function allow_or_block_on($uri)
	{		
		if($this->_eyes->is_allowed_on($uri))
			return true;

		if($this->is_api_call($uri))
			return Response::json(array('error' => 'forbidden'), 403);
		else
			return Response::make(View::make('bouncer::blocked'));
	}
}