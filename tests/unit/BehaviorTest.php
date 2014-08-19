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
 * Tests for Behavior
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @coversDefaultClass Indigo\Fuel\Doctrine\Behavior
 * @group              Doctrine
 */
class BehaviorTest extends Test
{
	/**
	 * Behavior object
	 *
	 * @var Behavior
	 */
	protected $behavior;

	/**
	 * {@inheritdoc}
	 */
	public function _before()
	{
		$this->behavior = new Behavior(['translatable', 'timestampable']);
	}

	/**
	 * @covers ::initReader
	 */
	public function testReader()
	{
		$cache = \Mockery::mock('Doctrine\\Common\\Cache\\Cache');

		$this->behavior->initReader($cache);
	}

	/**
	 * @covers ::registerMapping
	 * @covers ::__construct
	 */
	public function testRegisterMapping()
	{
		$driverChain = \Mockery::mock('Doctrine\\Common\\Persistence\\Mapping\\Driver\\MappingDriverChain');

		$driverChain->shouldReceive('addDriver')
			->andReturn(null);

		$this->behavior->registerMapping($driverChain);

		$behavior = new Behavior(['translatable', 'timestampable'], false);

		$behavior->registerMapping($driverChain);
	}

	/**
	 * @covers ::registerSubscribers
	 * @covers ::configSubscriber
	 */
	public function testRegisterSubscribers()
	{
		$evm = \Mockery::mock('Doctrine\\Common\\EventManager');

		$evm->shouldReceive('addEventSubscriber')
			->andReturn(null);

		$this->behavior->registerSubscribers($evm);
	}
}
