<?php
// Here you can initialize variables that will be available to your tests

$package = \Codeception\Configuration::projectDir();

\Package::load('doctrine', $package);

$module_paths = \Config::get('module_paths', []);

$module_paths[] = __DIR__.'/../_data/';

\Config::set('module_paths', $module_paths);
