<?php
/**
 * This file handles including the correct vendor autoloader.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */

$bootstrapped = false;
for($i = 0; $i <= 5; $i++) {
	$path = __DIR__ . '/' . implode($i > 0 ? array_fill(0, $i, '..') : array(), '/') . '/vendor/autoload.php';
	$root = realpath(__DIR__ . '/' . implode($i > 0 ? array_fill(0, $i, '..') : array(), '/'));
	if(file_exists($path)) {
		include_once $path;
		define('PATH_TO_PROJECT_ROOT', $root);
		$bootstrapped = true;
		break;
	}
}

if(!$bootstrapped) {
	echo 'Unable to load project dependencies, autoload file not found. Run the following commands:' . PHP_EOL .
		'curl -sS https://getcomposer.org/installer | php' . PHP_EOL .
		'php composer.phar install' . PHP_EOL;

	exit;
}