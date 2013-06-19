<?php

require __DIR__ . '/../libs/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Helpers::setup();
date_default_timezone_set('Europe/Prague');

if (extension_loaded('xdebug')) {
	xdebug_disable();
	Tester\CodeCoverage\Collector::start(__DIR__ . '/coverage.dat');
}

/**
 * @param $val
 * @return \Tester\TestCase
 */
function id($val) {
	return $val;
}