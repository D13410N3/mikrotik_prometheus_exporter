<?php
require_once 'init.php';

// CLI-access for debugging
// Script can be run from cli by using 'php metrics.php <device_ip>' 
if (php_sapi_name() == 'cli') {
	$_DEBUG = true;
	if ($argc >= 2) {
		$_GET['ip'] = $argv[1];
		// Checking second as if it is collector_name 
		if (isset($argv[2])) {
			$_DEBUG_COLL = $argv[2];
		} else {
			$_DEBUG_COLL = '';
		}
	} else {
		die('Usage: php metrics.php <device_ip> [<debug_controller_name>]');
	}
} else {
	$_DEBUG = false;
	$_DEBUG_COLL = '';
}

if (isset($_GET['ip'])) {
	$_IP = filter_var($_GET['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
	
	if ($_IP === false) {
		die('Couldn\'t determine device IP');
	}
	
	if (!isset($_DB['devices'][$_IP])) {
		die('No device with such IP in local database');
	}
	
	$_DEVICE = $_DB['devices'][$_IP];
	$_DEVICE['ip'] = $_IP;
	// Checking connect-settings
	// These values can't be empty
	$check = array('port', 'username', 'location');
	foreach ($check as $value) {
		if (@empty($_DEVICE[$value])) {
			if (@!empty($_DB['default'][$value])) {
				$_DEVICE[$value] = $_DB['default'][$value];
			} else {
				die('No device '.$value.' and default '.$value.' specified');
			}
		}
	}

	// Password can be empty, but shouldn't be :(
	if (!isset($_DEVICE['password'])) {
		$_DEVICE['password'] = empty($_DB['default']['password']) ? '' : $_DB['default']['password'];
	}

	// Building list of collectors for device
	if (@!empty($_DEVICE['collectors'])) {
		if (is_array($_DEVICE['collectors'])) {
			$_COLLECTORS = $_DEVICE['collectors'];
		} elseif (is_string($_DEVICE['collectors'])) {
			$_COLLECTORS = array(0 => $_DEVICE['collectors']);
		} else {
			$_COLLECTORS = array();
		}
	} elseif (@!empty($_DB['default']['collectors'])) {
		if (is_array($_DB['default']['collectors'])) {
			$_COLLECTORS = $_DB['default']['collectors'];
		} elseif (is_string($_DB['default']['collectors'])) {
			$_COLLECTORS = array(0 => $_DB['default']['collectors']);
		} else {
			$_COLLECTORS = array();
		}
	} else {
		$_COLLECTORS = array();
	}
	
	// Trying to initialize connection to the device
	$_API = new routeros_api();
	$conn = $_API -> connect($_DEVICE['ip'], $_DEVICE['port'], $_DEVICE['username'], $_DEVICE['password']);
	
	$_OUT = array();
	
	// Create predefined labels list
	#$_ARR = array('ip' => $_IP, 'hostname' => $_DEVICE['name'], 'location' => $_DEVICE['location']);
	
	# Removed hostname & replace it with prom relabeling
	$_ARR = array('location' => $_DEVICE['location']);
	
	// Throwing an error if something went wrong
	if ($conn == false) {
		$_OUT[] = prom(PREFIX.'_global_status', $_ARR, 0);
	} else {
		// Connection successful - time to start collecting information
		$_OUT[] = prom(PREFIX.'_global_status', $_ARR, 1);
		
		// Requiring collectors
		foreach (glob('collectors/*.php') as $collector_file) {
			require_once $collector_file;
		}
	}
	// saving last-check time
	$_last_check = time();
	$_OUT[] = prom(PREFIX.'_global_last_scrape_time', $_ARR, $_last_check);
	
	// Counting scrape_time
	$_end_time = microtime(true);
	$scrape_time = round($_end_time - $_start_time, 7) * 1000;
	$_OUT[] = prom(PREFIX.'_global_scrape_duration', $_ARR, $scrape_time);
	
	
	// Outputing the result
	$_OUT[] = PHP_EOL;
	if ($_DEBUG === true && @$argv[3] == 'dump') {
		var_dump($_OUT);
	} else {
		echo implode(PHP_EOL, $_OUT);
	}
	
} else {
	die('You didn\'t specify IP-address as GET-argument');
}