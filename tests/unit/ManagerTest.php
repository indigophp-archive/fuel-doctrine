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
					'auto_config' => true,
				]
			],
			2 => [
				[
					'behaviors' => ['translatable', 'timestampable'],
				]
			],
		];
	}

	/**
	 * @dataProvider configProvider
	 */
	public function testForge(array $config)
	{
		// Override config
		$c = \Config::get('doctrine', []);
		\Config::set('doctrine', array_merge($c, $config));

		$manager = Manager::forge();

		$this->assertInstanceOf('Doctrine\\Manager', $manager);
		$this->assertInstanceOf('Doctrine\\ORM\\EntityManager', $em = $manager->getEntityManager());
		$this->assertSame($em, $manager->getEntityManager());
		$this->assertInstanceOf('Doctrine\\Mapping', $manager->getMapping());
	}

	/**
	 * @expectedException LogicException
	 */
	public function testForgeFailure()
	{
		// Override config
		$config = [
			'auto_mapping' => true,
			'managers' => [
				'asd',
				'dsa',
			],
		];
		$c = \Config::get('doctrine', []);
		\Config::set('doctrine', array_merge($c, $config));

		$manager = Manager::forge();
	}
}
