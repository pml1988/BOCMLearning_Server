<?php

namespace Bouncer;

use Config;

class Rules
{
	protected static $_list = array();

	public static function list_rules()
	{
		if(static::$_list)
			return static::$_list;

		return static::$_list = static::retrieve_list();
	}

	protected static function retrieve_list()
	{
		return Config::get('bouncer::rules');
	}

	public static function all_paths()
	{
		return array_keys(static::list_rules());
	}

	public static function roles_for_path($path)
	{
		return array_get(static::retrieve_list(), $path, array());
	}

	public static function paths_for_role($role)
	{
		$paths = array();
		foreach(static::list_rules() as $path => $roles)
		{
			if(in_array($role, (array) $roles))
				$paths[] = $path;
		}

		return $paths;
	}

	public static function api_calls()
	{
		return Config::get('bouncer::api-calls');
	}
}