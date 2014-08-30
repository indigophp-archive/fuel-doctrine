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
 * Use this trait to implement Description field on entities
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait Description
{
	/**
	 * @var string
	 */
	private $description;

	/**
	 * Returns the description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 *
	 * @return this
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}
}
