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
 * Tests for Metadata
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @coversDefaultClass Indigo\Fuel\Doctrine\Metadata
 * @group              Doctrine
 */
class MetadataTest extends Test
{
	/**
	 * @covers ::create
	 */
	public function testMetadata()
	{
		$driver = Metadata::create('xml', '');

		$this->assertInstanceOf('Doctrine\\Common\\Persistence\\Mapping\\Driver\\MappingDriver', $driver);
	}

	/**
	 * @covers            ::create
	 * @expectedException Doctrine\ORM\ORMException
	 */
	public function testCacheFailure()
	{
		$cache = Metadata::create('fake', '');
	}
}
