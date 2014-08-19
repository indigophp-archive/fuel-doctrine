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
	 * Entity Manager config
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Mapping object
	 *
	 * @var Mapping
	 */
	protected $mapping;

	/**
	 * Entity Manager
	 *
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * {@inheritdoc}
	 */
	public static function forge($instance = null, Mapping $mapping = null)
	{
		// Try to get the default instance
		if ($instance === null)
		{
			static::$_instance = $instance = \Config::get('doctrine.default_manager', 'default');
		}

		// Remove some keys from config, not used anymore
		$config = \Config::get('doctrine', array());
		$config = \Arr::filter_keys($config, array('default_manager', 'managers'), true);

		// We have defined managers
		if ($managers = \Config::get('doctrine.managers', false))
		{
			// Get managers and retrive manager specific configuration
			$manager = \Arr::get($managers, $instance, array());

			$manager = array_merge($config, $manager);

			if (\Arr::get($manager, 'auto_mapping', false) and count($managers) > 1)
			{
				throw new \LogicException('Auto mapping is only possible if exactly one manager is used.');
			}
		}
		elseif ($instance === static::$_instance)
		{
			$manager = $config;
		}

		// We don't have any data
		if (empty($manager))
		{
			throw new \InvalidArgumentException('No manager data for this instance: ' . $instance);
		}

		if ($mapping === null)
		{
			$mapping = new \Doctrine\Mapping(\Arr::get($manager, 'mappings', array()), \Arr::get($manager, 'auto_mapping',false));
		}

		return static::newInstance($instance, new static($manager, $mapping));
	}

	/**
	 * Creates a new Manager
	 *
	 * @param array   $config
	 * @param Mapping $mapping
	 */
	public function __construct(array $config, Mapping $mapping)
	{
		$this->config = $config;
		$this->mapping = $mapping;
	}

	/**
	 * Creates a new Entity Manager
	 *
	 * @return EntityManager
	 */
	protected function createEntityManager()
	{
		// Cache can be null in case of auto setup
		if ($cache = \Arr::get($this->config, 'cache_driver', 'array'))
		{
			$cache = \Doctrine\Cache::create($cache);
		}

		// Auto or manual setup
		if (\Arr::get($this->config, 'auto_config', false))
		{
			$dev = \Arr::get($this->config, 'dev_mode', \Fuel::$env === \Fuel::DEVELOPMENT);
			$proxy_dir = \Arr::get($this->config, 'proxy_dir');

			$config = Setup::createConfiguration($dev, $proxy_dir, $cache);
		}
		else
		{
			$config = new Configuration;

			$config->setProxyDir(\Arr::get($this->config, 'proxy_dir'));
			$config->setProxyNamespace(\Arr::get($this->config, 'proxy_namespace'));
			$config->setAutoGenerateProxyClasses(\Arr::get($this->config, 'auto_generate_proxy_classes', false));

			if ($cache)
			{
				$config->setMetadataCacheImpl($cache);
				$config->setQueryCacheImpl($cache);
				$config->setResultCacheImpl($cache);
			}
		}

		$this->mapping->registerMapping($config);

		$conn = \Dbal::forge(\Arr::get($this->config, 'dbal', 'default'));
		$evm = $conn->getEventManager();

		if ($behaviors = \Arr::get($this->config, 'behaviors'))
		{
			$behavior = new \Doctrine\Behavior($behaviors);

			$behavior->initReader($cache);
			$behavior->registerMapping($config->getMetadataDriverImpl());
			$behavior->registerSubscribers($evm);
		}

		return $this->entityManager = EntityManager::create($conn, $config, $evm);
	}

	/**
	 * Returns the Entity Manager
	 *
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		if ($this->entityManager === null)
		{
			return $this->createEntityManager();
		}

		return $this->entityManager;
	}

	/**
	 * Returns the Mapping object
	 *
	 * @return Mapping
	 */
	public function getMapping()
	{
		return $this->mapping;
	}
}
