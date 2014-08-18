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
 * Tests for Cache
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @coversDefaultClass Indigo\Fuel\Doctrine\Cache
 * @group              Doctrine
 */
class CacheTest extends Test
{
	/**
	 * @covers ::create
	 */
	public function testCache()
	{
		$cache = Cache::create('array');

		$this->assertInstanceOf('Doctrine\\Common\\Cache\\Cache', $cache);
	}

	/**
	 * @covers            ::create
	 * @expectedException Doctrine\ORM\ORMException
	 */
	public function testCacheFailure()
	{
		$cache = Cache::create('fake');
	}
}
