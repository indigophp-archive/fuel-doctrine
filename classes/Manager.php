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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\ORMException;

/**
 * Entity Manager Facade
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Manager extends \Facade
{
	use \Indigo\Core\Facade\Instance;

	/**
	 * {@inheritdoc}
	 */
	protected static $_config = 'doctrine';

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
	 * Map metadata driver types to class names
	 *
	 * @var array
	 */
	protected static $metadata_drivers = array(
		'php'             => 'PHPDriver',
		'simplified_xml'  => 'SimplifiedXmlDriver',
		'simplified_yaml' => 'SimplifiedYamlDriver',
		'xml'             => 'XmlDriver',
		'yaml'            => 'YamlDriver'
	);

	/**
	 * {@inheritdoc}
	 */
	public static function forge($instance = 'default')
	{
		$conf = \Config::get('doctrine.' . $instance, array());

		// Cache can be null in case of auto setup
		if ($cache = \Arr::get($conf, 'cache_driver', 'array'))
		{
			$cache = static::createCache($cache);
		}

		// Auto or manual setup
		if (\Arr::get($conf, 'auto_config', false))
		{
			$dev = \Arr::get($conf, 'dev_mode', \Fuel::$env === \Fuel::DEVELOPMENT);
			$proxy_dir = \Arr::get($conf, 'proxy_dir');

			$config = Setup::createConfiguration($dev, $proxy_dir, $cache);
		}
		else
		{
			$config = new Configuration;

			$config->setProxyDir(\Arr::get($conf, 'proxy_dir'));
			$config->setProxyNamespace(\Arr::get($conf, 'proxy_namespace'));
			$config->setAutoGenerateProxyClasses(\Arr::get($conf, 'auto_generate_proxy_classes', false));

			if ($cache)
			{
				$config->setMetadataCacheImpl($cache);
				$config->setQueryCacheImpl($cache);
				$config->setResultCacheImpl($cache);
			}
		}

		$metadata_driver = \Arr::get($conf, 'metadata_driver');
		$metadata_path = \Arr::get($conf, 'metadata_path');

		if ($metadata_driver === 'annotation')
		{
			$metadata = $config->newDefaultAnnotationDriver($metadata_path);
		}
		else
		{
			$metadata = static::createMetadata($metadata_driver, $metadata_path);
		}

		$config->setMetadataDriverImpl($metadata);

		$conn = \Dbal::forge(\Arr::get($conf, 'dbal', 'default'));
		$em = $conn->getEventManager();

		return static::newInstance($instance, EntityManager::create($conn, $config, $em));
	}

	/**
	 * Creates a new Cache object
	 *
	 * @param string $driver Driver name or class name
	 *
	 * @return Doctrine\Common\Cache\Cache
	 */
	public static function createCache($driver)
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

	/**
	 * Creates a new Mapping Driver object
	 *
	 * @param string $driver Driver name or class name
	 *
	 * @return Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
	 */
	public static function createMetadata($driver, $path)
	{
		$class = $driver;

		if (array_key_exists($driver, static::$metadata_drivers))
		{
			$class = 'Doctrine\\ORM\\Mapping\\Driver\\' . static::$metadata_drivers[$driver];
		}

		if (class_exists($class))
		{
			return new $class($path);
		}

		throw new ORMException('Invalid metadata driver: ' . $driver);
	}
}
