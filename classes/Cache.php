<?php

/*
 * This file is part of the Fuel Doctrine package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Fuel\Doctrine;

use Doctrine\ORM\ORMException;

/**
 * Cache
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Cache
{
	/**
	 * Map cache driver types to class names
	 *
	 * @var array
	 */
	protected static $cache_drivers = array(
		'array'    => 'ArrayCache',
		'apc'      => 'ApcCache',
		'xcache'   => 'XcacheCache',
		'wincache' => 'WinCache',
		'zend'     => 'ZendDataCache',
	);

	/**
	 * Creates a new Cache object
	 *
	 * @param string $driver Driver name or class name
	 *
	 * @return Doctrine\Common\Cache\Cache
	 */
	public static function create($driver)
	{
		$class = $driver;

		if (array_key_exists($driver, static::$cache_drivers))
		{
			$class = 'Doctrine\\Common\\Cache\\' . static::$cache_drivers[$driver];
		}

		if (class_exists($class))
		{
			return new $class;
		}

		throw new ORMException('Invalid cache driver: ' . $driver);
	}
}
