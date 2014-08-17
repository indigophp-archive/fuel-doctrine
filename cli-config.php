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
 * Copy this file to the project root
 */

// Make this path valid
require_once __DIR__.'/fuel/app/bootstrap.php';

// Use DB env var by default to decide which configuration should be used
$db = getenv('DB') ?: 'default';

$entityManager = \Doctrine\Manager::forge($db);

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
));
