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
	protected $manager;

	public function _before()
	{
		is_dir(APPPATH.'classes/Entity') === false and mkdir(APPPATH.'classes/Entity');

		$root = \Codeception\Configuration::projectDir();

		$db = require $root.'/fuel/packages/dbal/tests/unit/config/db.php';
		$config = require __DIR__.'/config/doctrine.php';

		\Config::set('db', $db);
		\Config::set('doctrine', $config);

		$this->manager = Manager::forge();

		\Package::load('auth');
		\Module::load(['module', 'module2', 'module3', 'module4', 'module5', 'module6']);
	}

	/**
	 * Loads advanced config
	 */
	public function advancedConfig()
	{
		$config = require __DIR__.'/config/advanced.php';
		$config = array_merge(\Config::get('doctrine', []), $config);

		\Config::set('doctrine', $config);
	}

	/**
	 * @covers ::forge
	 */
	public function testForge()
	{
		\Config::delete('doctrine.managers');

		$manager = Manager::forge();

		$this->assertInstanceOf('Indigo\\Fuel\\Doctrine\\Manager', $manager);
	}

	/**
	 * @covers ::forge
	 */
	public function testAdvancedForge()
	{
		$this->advancedConfig();

		\Config::set('doctrine.mapping.auto', false);

		$manager = Manager::forge();

		$this->assertInstanceOf('Indigo\\Fuel\\Doctrine\\Manager', $manager);
	}

	/**
	 * @covers            ::forge
	 * @expectedException LogicException
	 */
	public function testForgeAutoMapping()
	{
		$this->advancedConfig();

		Manager::forge();
	}

	/**
	 * @covers            ::forge
	 * @expectedException InvalidArgumentException
	 */
	public function testForgeInvalid()
	{
		Manager::forge('invalid');
	}

	/**
	 * @covers ::__construct
	 * @covers ::autoLoadMappingInfo
	 */
	public function testConstruct()
	{
		$config = [
			'mapping' => [
				'auto' => true,
			],
		];

		$manager = new Manager($config);

		$this->assertArrayHasKey('mapping', $manager->getConfig());
	}

	/**
	 * @covers ::getMappings
	 * @covers ::setMappings
	 */
	public function testMappings()
	{
		$this->assertSame($this->manager, $this->manager->setMappings('test', []));
		$this->assertEquals($this->manager->getConfig('mappings'), $this->manager->getMappings());
	}

	/**
	 * @covers ::getConfigPath
	 * @covers ::getClassPath
	 * @covers ::getObjectName
	 */
	public function testDefault()
	{
		$this->assertEquals('config/doctrine/', $this->manager->getConfigPath());
		$this->assertEquals('classes/', $this->manager->getClassPath());
		$this->assertEquals('Entity', $this->manager->getObjectName());
	}

	public function testParse()
	{
		$this->manager->parseMappingInfo();

		$actual = $this->manager->getMappings();

		$expected = array_filter(\Config::get('doctrine.mappings'));
		$expected['test']['is_component'] = false;

		$this->assertArrayHasKey('test', $actual);
		$this->assertEquals($expected['test'], $actual['test']);

		$this->assertArrayHasKey('module::module', $actual);
		$this->assertArrayHasKey('module2::module', $actual);
		$this->assertArrayHasKey('module3::module', $actual);
		$this->assertArrayHasKey('module4::module', $actual);
		$this->assertArrayHasKey('module5::module', $actual);
		$this->assertArrayHasKey('module6::module', $actual);

		$this->assertEquals('xml', $actual['module::module']['type']);
		$this->assertEquals('simplified_xml', $actual['module5::module']['type']);
		$this->assertEquals('yml', $actual['module2::module']['type']);
		$this->assertEquals('simplified_yml', $actual['module6::module']['type']);
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

		$this->manager->registerMapping($config);
	}

	/**
	 * @covers ::createEntityManager
	 * @covers ::getEntityManager
	 */
	public function testEntityManager()
	{
		$em = $this->manager->getEntityManager();

		$this->assertInstanceOf('Doctrine\\ORM\\EntityManager', $em);
		$this->assertSame($em, $this->manager->getEntityManager());
	}

	/**
	 * @covers ::createEntityManager
	 */
	public function testEntityManagerAutoConfig()
	{
		$em = $this->manager->setConfig('auto_config', true);
		$em = $this->manager->getEntityManager();

		$this->assertInstanceOf('Doctrine\\ORM\\EntityManager', $em);
	}

	/**
	 * @covers ::registerBehaviors
	 * @covers ::configureBehavior
	 */
	public function testBehavior()
	{
		$this->manager->setConfig('behaviors', [
			'translatable'
		]);

		$em = $this->manager->getEntityManager();

		$evm = $em->getEventManager();

		$this->assertTrue($evm->hasListeners('postLoad'));
		$this->assertTrue($evm->hasListeners('postPersist'));
		$this->assertTrue($evm->hasListeners('preFlush'));
		$this->assertTrue($evm->hasListeners('onFlush'));
		$this->assertTrue($evm->hasListeners('loadClassMetadata'));
	}

	/**
	 * @covers            ::registerBehaviors
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidBehavior()
	{
		$this->manager->setConfig('behaviors', [
			'fakeable'
		]);

		$em = $this->manager->getEntityManager();
	}
}
