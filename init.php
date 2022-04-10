<?php
$_start_time = microtime(true);

// Predefine prefix for metrics-name (mt_status, mt_scrape_time etc.)

define('PREFIX', 'mikrotik');
header('Content-type: text/plain');

require_once 'mikrotik_api.php';

// Function to construct prometheus string
function prom($name = PREFIX.'_default', $args = array('error' => 'Check prom-function args'), $value = 0) {
	$_args = array();
	
	foreach($args as $key => $arg) {
		$_args[] = $key.'="'.$arg.'"';
	}
	
	return $name.' {'.implode(', ', $_args).'} '.$value;
}

// Function to decide if there's a need to use this collector
function checkCollector($col = 'test', $list = array()) {
	return (array_search($col, $list) === false && array_search('all', $list) === false) ? false : true;
}

// Start parsing
$_DB = @yaml_parse_file('db.yml');

// Dropping an error if something is wrong

if ($_DB === false) {
	die('Broken YAML Database file');
}