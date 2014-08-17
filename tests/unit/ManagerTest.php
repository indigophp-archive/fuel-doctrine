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

use Codeception\TestCase\Test;

/**
 * Tests for Manager
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @coversDefaultClass Indigo\Fuel\Doctrine\Manager
 * @group              Doctrine
 */
class ManagerTest extends Test
{
	/**
	 * {@inheritdoc}
	 */
	public function _before()
	{
		$root = \Codeception\Configuration::projectDir();

		$db = require $root.'/fuel/packages/dbal/tests/unit/config.php';
		$config = require __DIR__.'/config.php';

		\Config::set('db', $db);
		\Config::set('doctrine', $config);
	}

	/**
	 * Provides config override
	 *
	 * @return []
	 */
	public function configProvider()
	{
		return [
			0 => [[]],
			1 => [
				[
					'metadata_driver' => 'annotation',
				]
			],
			2 => [
				[
					'auto_config' => true,
				]
			],
		];
	}

	/**
	 * @covers       ::forge
	 * @dataProvider configProvider
	 */
	public function testForge(array $config)
	{
		// Override config
		$c = \Config::get('doctrine.default', []);
		\Config::set('doctrine.default', array_merge($c, $config));

		$em = Manager::forge();

		$this->assertInstanceOf('Doctrine\\ORM\\EntityManager', $em);
	}

	/**
	 * @covers ::createCache
	 */
	public function testCache()
	{
		$cache = Manager::createCache('array');

		$this->assertInstanceOf('Doctrine\\Common\\Cache\\Cache', $cache);
	}

	/**
	 * @covers            ::createCache
	 * @expectedException Doctrine\ORM\ORMException
	 */
	public function testCacheFailure()
	{
		$cache = Manager::createCache('fake');
	}

	/**
	 * @covers ::createMetadata
	 */
	public function testMetadata()
	{
		$cache = Manager::createMetadata('xml', '');

		$this->assertInstanceOf('Doctrine\\Common\\Persistence\\Mapping\\Driver\\MappingDriver', $cache);
	}

	/**
	 * @covers            ::createMetadata
	 * @expectedException Doctrine\ORM\ORMException
	 */
	public function testMetadataFailure()
	{
		$cache = Manager::createMetadata('fake', '');
	}
}
