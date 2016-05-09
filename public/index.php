<?php

try {

	if (version_compare(phpversion(), '5.5.0', '<')) {
		throw new Exception('PHP >=5.5 required.');
	}
	define('APP_PATH', dirname(dirname(__FILE__)));

	$sBattleLogs = APP_PATH.'/battlelogs/dummy.txt';
	if (!is_writable(dirname($sBattleLogs))) {
		throw new Exception('Please allow the battlelogs folders to be writable.');
	}

	require '../classes/Autoloader.php';

	$autoloaderPSR = new \Autoloader('', APP_PATH . '/classes');
	$autoloaderPSR->register();

	$oControl = new Control();

	$sAction = 'Setup';
	if (isset($_GET['action'])) {
		$sAction = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['action']);
	}

	$sAction = ucfirst($sAction);
	if (method_exists($oControl, 'action' . ($sAction))) {
		$oControl->{'action' . $sAction}();
	} else {
		throw new \Exception('Cannot find control method.', 2000);
	}

}catch (\Exception $e) {
	echo "Error: ".$e->getCode().": ".$e->getMessage();
}

