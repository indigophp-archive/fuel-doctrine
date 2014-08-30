<?php

/*
 * This file is part of the Fuel Doctrine package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Fuel\Doctrine\Field;

/**
 * Use this trait to implement Name field on entities
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait Name
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * Returns the name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets the name
	 *
	 * @param string $name
	 *
	 * @return this
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}
}