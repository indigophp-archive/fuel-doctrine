<?php

/*
 * This file is part of the Fuel Doctrine package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Fuel\Doctrine\Behavior;

use Codeception\TestCase\Test;

/**
 * Tests for Behaviors
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @group Doctrine
 */
class BehaviorTest extends Test
{
	/**
	 * Dummy Entity object
	 *
	 * @var DummyEntity
	 */
	protected $entity;

	public function _before()
	{
		$this->entity = new \DummyEntity;
	}

	/**
	 * @covers Indigo\Fuel\Doctrine\Behavior\Slug::getSlug
	 * @covers Indigo\Fuel\Doctrine\Behavior\Slug::setSlug
	 */
	public function testSlug()
	{
		$this->assertSame($this->entity, $this->entity->setSlug('test'));
		$this->assertEquals('test', $this->entity->getSlug());
	}

	/**
	 * @covers Indigo\Fuel\Doctrine\Behavior\SoftDelete::getDeletedAt
	 * @covers Indigo\Fuel\Doctrine\Behavior\SoftDelete::setDeletedAt
	 */
	public function testSoftDelete()
	{
		$date = new \DateTime();

		$this->assertSame($this->entity, $this->entity->setDeletedAt($date));
		$this->assertEquals($date, $this->entity->getDeletedAt());
	}

	/**
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::getLeft
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::setLeft
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::getRight
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::setRight
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::getLevel
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::setLevel
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::getParent
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::setParent
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::getChildren
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::addChild
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::hasChild
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::removeChild
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::isRoot
	 * @covers Indigo\Fuel\Doctrine\Behavior\Tree::initTree
	 */
	public function testTree()
	{
		$parent = new \DummyEntity;
		$child = new \DummyEntity;

		$this->assertSame($this->entity, $this->entity->setLeft(1));
		$this->assertEquals(1, $this->entity->getLeft());
		$this->assertSame($this->entity, $this->entity->setRight(1));
		$this->assertEquals(1, $this->entity->getRight());
		$this->assertSame($this->entity, $this->entity->setLevel(1));
		$this->assertEquals(1, $this->entity->getLevel());
		$this->assertSame($this->entity, $this->entity->setParent($parent));
		$this->assertSame($parent, $this->entity->getParent());
		$this->assertSame($this->entity, $this->entity->addChild($child));
		$this->assertInstanceOf('Doctrine\\Common\\Collections\\ArrayCollection', $this->entity->getChildren());
		$this->assertTrue($this->entity->hasChild($child));
		$this->assertFalse($parent->hasChild($this->entity));
		$this->assertSame($this->entity, $child->getParent());
		$this->assertNull($this->entity->removeChild($child));
		$this->assertFalse($this->entity->hasChild($child));
		$this->assertTrue($parent->isRoot());
		$this->assertFalse($this->entity->isRoot());
	}
}