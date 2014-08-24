<?php

/*
 * This file is part of the Fuel Doctrine package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
	'dbal'                        => 'default',
	'proxy_dir'                   => '/tmp',
	'proxy_namespace'             => 'PrOxYnAmEsPaCe',
	'auto_generate_proxy_classes' => true,
	'cache_driver'                => 'array',
	'mapping'                     => [
		'auto' => true,
	],
	'mappings'                    => [
		'test' => [
			'type'   => 'xml',
			'dir'    => '/tmp',
			'prefix' => '',
		],
		'false' => false,
		'auth' => [
			'is_component' => true,
		],
		'module' => [
			'dir'          => ['config/doctrine'],
			'alias'        => 'Mod',
			'is_component' => true,
		],
		'module2' => [
			'dir'  => 'config/doctrine',
		],
		'module5::module' => [
			'is_component' => true,
		],
		'module6::fake' => [
			'is_component' => true,
		],
	],
];
