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
 * Tests for Mapping
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @coversDefaultClass Indigo\Fuel\Doctrine\Mapping
 * @group              Doctrine
 */
class MappingTest extends Test
{
	/**
	 * Mapping object
	 *
	 * @var Mapping
	 */
	protected $mapping;

	/**
	 * Mapping info
	 *
	 * @var []
	 */
	protected $mappingInfo = [
		'test' => [
			'type'   => 'xml',
			'dir'    => '/tmp',
			'prefix' => '',
		],
		'false' => false,
		'auth' => [
			'is_component' => true,
		],
		'module' => [
			'dir'          => ['config/doctrine'],
			'alias'        => 'Mod',
			'is_component' => true,
		],
		'module2' => [
			'dir'  => 'config/doctrine',
		],
	];

	public function _before()
	{
		is_dir(APPPATH.'classes/Entity') === false and mkdir(APPPATH.'classes/Entity');
		$this->mapping = new Mapping($this->mappingInfo);
	}

	public function _after()
	{
		\Package::loaded('auth') and \Package::unload('auth');
		\Module::unload('module');
	}

	/**
	 * @covers ::getExtension
	 * @covers ::getConfigPath
	 * @covers ::getClassPath
	 * @covers ::getObjectName
	 */
	public function testDefault()
	{
		$this->assertEquals('dcm', $this->mapping->getExtension());
		$this->assertEquals('config/doctrine', $this->mapping->getConfigPath());
		$this->assertEquals('classes', $this->mapping->getClassPath());
		$this->assertEquals('Entity', $this->mapping->getObjectName());
	}

	public function testParse()
	{
		\Package::load('auth');
		$actual = $this->mapping->parseMappingInfo($this->mappingInfo, false);

		$expected = array_filter($this->mappingInfo);
		$expected['test']['is_component'] = false;

		$this->assertArrayHasKey('test', $actual);
		$this->assertEquals($expected['test'], $actual['test']);
	}

	public function testAutoParse()
	{
		\Package::load('auth');
		\Module::load('module', __DIR__.'/../_data/module/');
		\Module::load('module2', __DIR__.'/../_data/module2/');
		\Module::load('module3', __DIR__.'/../_data/module3/');
		\Module::load('module4', __DIR__.'/../_data/module4/');
		$actual = $this->mapping->parseMappingInfo($this->mappingInfo, true);

		$this->assertArrayHasKey('module::module', $actual);
		$this->assertArrayHasKey('module2::module', $actual);
		$this->assertArrayHasKey('module3::module', $actual);
		$this->assertArrayHasKey('module4::module', $actual);

		$this->assertEquals('xml', $actual['module::module']['type']);
		$this->assertEquals('yml', $actual['module2::module']['type']);
		$this->assertEquals('php', $actual['module3::module']['type']);
		$this->assertEquals('annotation', $actual['module4::module']['type']);
	}

	public function testRegister()
	{
		$config = \Mockery::mock('Doctrine\\ORM\\Configuration');

		$config->shouldReceive('newDefaultAnnotationDriver')
			->andReturn(\Mockery::mock('Doctrine\\ORM\\Mapping\\Driver\\AnnotationDriver'));

		$config->shouldReceive('setMetadataDriverImpl')
			->andReturn(null);

		$config->shouldReceive('setEntityNamespaces')
			->andReturn(null);

		\Module::load('module', __DIR__.'/../_data/module/');
		\Module::load('module4', __DIR__.'/../_data/module4/');

		$mapping = new Mapping($this->mappingInfo, true);

		$mapping->registerMapping($config);
	}

	/**
	 * @covers ::createMetadataDriver
	 */
	public function testCreateDriver()
	{
		$cache = Mapping::createMetadataDriver('xml', '');

		$this->assertInstanceOf('Doctrine\\Common\\Persistence\\Mapping\\Driver\\MappingDriver', $cache);
	}

	/**
	 * @covers            ::createMetadataDriver
	 * @expectedException Doctrine\ORM\ORMException
	 */
	public function testCreateDriverFailure()
	{
		$cache = Mapping::createMetadataDriver('fake', '');
	}
}
