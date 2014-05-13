<?php

namespace Bouncer;

use IoC;

class Eyes
{
	protected $_user = null;
	protected $_roles = array();

	protected static $_roles_extractor = null;

	public static function on($user)
	{
		return new static($user);
	}

	public static function roles_extractor()
	{
		if(static::$_roles_extractor)
			return static::$_roles_extractor;

		return static::$_roles_extractor = IoC::resolve('bouncer: roles_extractor');
	}

	public function __construct($user)
	{
		$this->_user = $user;
	}

	public function user()
	{
		return $this->_user;
	}

	public function roles()
	{
		if($this->_roles)
			return $this->_roles;

		$roles_extractor = static::roles_extractor();

		return $this->_roles = $roles_extractor($this->user());
	}

	public function is_allowed_on($uri)
	{
		if( null === $matched_path = $this->find_best_matched_path(Rules::all_paths(), $uri) )
			return true;

		return in_array($matched_path, $this->allowed_paths());
	}

	protected function find_best_matched_path($paths, $uri)
	{
		$match = null;
		foreach($paths as $p)
		{
			if( starts_with($uri, $p) and ( $match === null or count(explode('/', $p)) > count(explode('/', $match)) ) ) {
				$match = $p;
			}
		}

		return $match;
	}

	public function allowed_paths()
	{
		$paths = array();
		foreach($this->roles() as $role)
			$paths = array_merge($paths, Rules::paths_for_role($role));

		return array_values(array_unique($paths));
	}
}