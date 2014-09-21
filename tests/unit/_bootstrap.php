<?php
// Here you can initialize variables that will be available to your tests

$package = \Codeception\Configuration::projectDir();

\Package::load('dependency', $package.'/fuel/packages/dependency/');
\Package::load('doctrine', $package);

$module_paths = \Config::get('module_paths', []);

$module_paths[] = \Codeception\Configuration::dataDir();

\Config::set('module_paths', $module_paths);
