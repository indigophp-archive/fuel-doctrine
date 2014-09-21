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

use Indigo\Fuel\Dependency\Container as DiC;
use Symfony\Component\Console\Helper\DialogHelper;
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
	 * Console version const
	 */
	const VERSION = '1.0.0';

	/**
	 * DBAL connection name
	 *
	 * @var string
	 */
	protected $db;

	/**
	 * Create task
	 */
	public function __construct()
	{
		// Removes oil args
		array_shift($_SERVER['argv']);
		array_shift($_SERVER['argv']);
		$_SERVER['argc'] -= 2;

		$this->db = getenv('DB') ?: '__default__';
	}

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
		$em = DiC::multiton('doctrine.manager', $this->db)->getEntityManager();

		$helperSet = ConsoleRunner::createHelperSet($em);
		$commands = array();

		if (class_exists('Doctrine\\DBAL\\Migrations\\Migration'))
		{
			$helperSet->set(new DialogHelper(), 'dialog');

			$commands = array(
				new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
				new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
				new \Doctrine\DBAL\Migrations\Tools\Console\Command\LatestCommand(),
				new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
				new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
				new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand(),
				new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
			);
		}

		ConsoleRunner::run($helperSet, $commands);
	}
}
