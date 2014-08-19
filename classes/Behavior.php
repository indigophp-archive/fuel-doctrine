<?php

/*
 * This file is part of the Indigo Doctrine package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Fuel\Doctrine;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Gedmo\DoctrineExtensions;

/**
 * Behavior
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Behavior
{
	/**
	 * Annotation Reader used for Doctrine Extensions
	 *
	 * @var AnnotationReader
	 */
	protected $reader;

	/**
	 * Behavior config
	 *
	 * @var array
	 */
	protected $behaviors = array();

	/**
	 * Register only superclass
	 * @var boolean
	 */
	protected $superclass = true;

	/**
	 * Creates a new Behavior
	 *
	 * @param array   $behaviors
	 * @param boolean $superclass
	 */
	public function __construct(array $behaviors, $superclass = true)
	{
		$this->behaviors = $behaviors;
		$this->superclass = $superclass;
	}

	/**
	 * Creates a new Reader
	 *
	 * @param Cache $cache
	 */
	public function initReader(Cache $cache = null)
	{
		$this->reader = new AnnotationReader;

		if ($cache)
		{
			$this->reader = new CachedReader($this->reader, $cache);
		}
	}

	/**
	 * Registers behavior to mapping driver
	 *
	 * @param  MappingDriverChain $driver
	 */
	public function registerMapping(MappingDriverChain $driver)
	{
		if ($this->superclass)
		{
			DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
				$driver,
				$this->reader
			);

			return;
		}

		DoctrineExtensions::registerMappingIntoDriverChainORM(
			$driver,
			$this->reader
		);
	}

	/**
	 * Registers subscribers to Event Manager
	 *
	 * @param EventManager $evm
	 */
	public function registerSubscribers(EventManager $evm)
	{
		foreach ($this->behaviors as $behavior)
		{
			$objectName = ucfirst($behavior);
			$class = 'Gedmo\\'.$objectName.'\\'.$objectName.'Listener';

			$class = new $class;
			$class->setAnnotationReader($this->reader);

			$this->configSubscriber($behavior, $class);

			$evm->addEventSubscriber($class);
		}
	}

	/**
	 * Configures Subscriber
	 * @param  string          $behavior
	 * @param  EventSubscriber $es
	 */
	protected function configSubscriber($behavior, EventSubscriber $es)
	{
		switch ($behavior) {
			case 'translatable':
				$es->setTranslatableLocale(\Config::get('language', 'en'));
				$es->setDefaultLocale(\Config::get('language_fallback', 'en'));
				break;

			default:
				break;
		}
	}
}
