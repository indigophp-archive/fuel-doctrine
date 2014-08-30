<?php

/*
 * This file is part of the Fuel Doctrine package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Dummy Entity
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class DummyEntity
{
	/**
	 * Fields
	 */
	use \Indigo\Fuel\Doctrine\Field\Id;
	use \Indigo\Fuel\Doctrine\Field\Name;
	use \Indigo\Fuel\Doctrine\Field\Description;

	/**
	 * Behaviors
	 */
	use \Indigo\Fuel\Doctrine\Behavior\SoftDelete;
	use \Indigo\Fuel\Doctrine\Behavior\Slug;
	use \Indigo\Fuel\Doctrine\Behavior\Tree;

	public function __construct()
	{
		$this->initTree();
	}
}
