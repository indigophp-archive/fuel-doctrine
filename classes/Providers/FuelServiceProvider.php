<?php

/*
 * This file is part of the Fuel DBAL package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Fuel\Doctrine\Providers;

use Fuel\Dependency\ServiceProvider;

/**
 * Provides Doctrine service
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FuelServiceProvider extends ServiceProvider
{
	/**
	 * {@inheritdoc}
	 */
	public $provides = [
		'doctrine.metadata.php',
		'doctrine.metadata.xml',
		'doctrine.metadata.simplified_xml',
		'doctrine.metadata.yml',
		'doctrine.metadata.simplified_yml',
		'doctrine.cache',
		'doctrine.cache.array',
		'doctrine.cache.apc',
		'doctrine.cache.xcache',
		'doctrine.cache.wincache',
		'doctrine.cache.zend',
	];

	/**
	 * Default configuration values
	 *
	 * @var []
	 */
	protected $defaultConfig = [];

	public function __construct()
	{
		\Config::load('doctrine', true);

		$config = \Config::get('doctrine', []);
		$this->defaultConfig = \Arr::filter_keys($config, ['connections', 'types'], true);

		// We don't have defined managers
		if ($managers = \Arr::get($config, 'managers', false) and ! empty($managers))
		{
			\Config::set('dbal.managers.__default__', $this->defaultConfig);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function provide()
	{
		// Metadata drivers
		$this->register('doctrine.metadata.php', function($dic, $paths = [])
		{
			$dic->resolve('Doctrine\\ORM\\Mapping\\Driver\\PHPDriver', [$paths]);
		});

		$this->register('doctrine.metadata.xml', function($dic, $paths = [])
		{
			$dic->resolve('Doctrine\\ORM\\Mapping\\Driver\\XmlDriver', [$paths]);
		});

		$this->register('doctrine.metadata.simplified_xml', function($dic, $paths = [])
		{
			$dic->resolve('Doctrine\\ORM\\Mapping\\Driver\\SimplifiedXmlDriver', [$paths]);
		});

		$this->register('doctrine.metadata.yml', function($dic, $paths = [])
		{
			$dic->resolve('Doctrine\\ORM\\Mapping\\Driver\\YamlDriver', [$paths]);
		});

		$this->register('doctrine.metadata.simplified_yml', function($dic, $paths = [])
		{
			$dic->resolve('Doctrine\\ORM\\Mapping\\Driver\\SimplifiedYamlDriver', [$paths]);
		});

		// Caches
		$this->register('doctrine.cache', 'Doctrine\\Common\\Cache\\ArrayCache');
		$this->register('doctrine.cache.array', 'Doctrine\\Common\\Cache\\ArrayCache');
		$this->register('doctrine.cache.apc', 'Doctrine\\Common\\Cache\\ApcCache');
		$this->register('doctrine.cache.xcache', 'Doctrine\\Common\\Cache\\XcacheCache');
		$this->register('doctrine.cache.wincache', 'Doctrine\\Common\\Cache\\WincacheCache');
		$this->register('doctrine.cache.zend', 'Doctrine\\Common\\Cache\\ZendDataCache');
	}
}
