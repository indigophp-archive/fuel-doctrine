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
 * Metadata
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Metadata
{
	/**
	 * Map driver types to class names
	 *
	 * @var array
	 */
	protected static $drivers = array(
		'php'             => 'PHPDriver',
		'simplified_xml'  => 'SimplifiedXmlDriver',
		'simplified_yaml' => 'SimplifiedYamlDriver',
		'xml'             => 'XmlDriver',
		'yml'             => 'YamlDriver'
	);

	/**
	 * Creates a new Mapping Driver object
	 *
	 * @param string       $driver Driver name or class name
	 * @param string|array $path   Path used for metadata driver
	 *
	 * @return Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
	 */
	public static function create($driver, $path)
	{
		if (array_key_exists($driver, static::$drivers))
		{
			$class = 'Doctrine\\ORM\\Mapping\\Driver\\' . static::$drivers[$driver];

			return new $class($path);
		}

		throw new ORMException('Invalid metadata driver: ' . $driver);
	}
}
