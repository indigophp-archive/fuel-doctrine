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
	public static function forge($instance = 'default', Mapping $mapping = null)
	{
		$config = \Config::get('doctrine', array());
		$managers = \Arr::get($config, 'managers', array());
		$manager = \Arr::get($managers, $instance, array());

		\Arr::delete($config, 'managers');

		$config = array_merge($config, $manager);

		if (\Arr::get($config, 'auto_mapping', false) and count($managers) > 1)
		{
			throw new \LogicException('Auto mapping is only possible if exactly one manager is used.');
		}

		if ($mapping === null)
		{
			$mapping = new \Doctrine\Mapping(\Arr::get($config, 'mappings', array()), \Arr::get($config, 'auto_mapping',false));
		}

		return static::newInstance($instance, new static($config, $mapping));
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
