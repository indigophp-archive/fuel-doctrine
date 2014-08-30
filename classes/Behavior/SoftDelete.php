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

/**
 * Use this trait to implement soft deletable behavior on entities
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait SoftDelete
{
	/**
	 * @var \DateTime
	 */
	private $deletedAt;

	/**
	 * Returns deletedAt
	 *
	 * @return \DateTime
	 */
	public function getDeletedAt()
	{
		return $this->deletedAt;
	}

	/**
	 * Sets deletedAt
	 *
	 * @param \DateTime $deletedAt
	 *
	 * @return self
	 */
	public function setDeletedAt($deletedAt)
	{
		$this->deletedAt = $deletedAt;

		return $this;
	}
}