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
 * Use this trait to implement ID field on entities
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait Id
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * Returns the id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
}
