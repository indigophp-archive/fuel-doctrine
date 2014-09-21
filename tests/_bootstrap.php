<?php
// This is global bootstrap for autoloading

use Indigo\Fuel\Dependency\Container as DiC;

$autoloader = require __DIR__.'/../vendor/autoload.php';

DiC::getDic()->inject('autoloader', $autoloader);
