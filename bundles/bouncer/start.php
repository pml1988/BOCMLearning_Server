<?php

Autoloader::namespaces(array(
	'Bouncer' => Bundle::path('bouncer') . 'src'
));

Autoloader::alias('Bouncer\\Bouncer', 'Bouncer');

IoC::register('bouncer: roles_extractor', function () {
	return function ($user)
    {
		return array_map(function ($role)
        {
            return $role;
        }, unserialize($user->roles));
	};
});