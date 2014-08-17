<?php

/*
 * This file is part of the Fuel Doctrine package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuel\Tasks;

use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

/**
 * This ugly hack makes it possible to run doctrine commands from fuel context
 *
 * cli-config.php file didn't really work, so using this for now
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Doctrine
{
	/**
	 * Main Doctrine method
	 *
	 * Usage (from command line):
	 *
	 * php oil r doctrine
	 *
	 * @return string
	 */
	public function run()
	{
		$db = getenv('DB') ?: 'default';

		$entityManager = \Doctrine\Manager::forge($db);

		$helperSet = new HelperSet(array(
			'em' => new EntityManagerHelper($entityManager)
		));

		// Remove oil args
		array_shift($_SERVER['argv']);
		array_shift($_SERVER['argv']);
		$_SERVER['argc'] -= 2;

		ConsoleRunner::run($helperSet);
	}
}
