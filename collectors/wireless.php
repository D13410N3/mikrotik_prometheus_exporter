<?php
// Collector name
$_COLLECTOR['enable'] = 0;
$_COLLECTOR['name'] = 'wireless';
$_COLLECTOR['cmd'] = '/interface/wireguard/peers/print';
if (checkCollector($_COLLECTOR['name'], $_COLLECTORS) && $_COLLECTOR['enable'] == 1) {
	$_coll_start_time = microtime(true);
	
	// Making new array
	$_ARR_COLL = $_ARR + array('collector' => $_COLLECTOR['name']);
	
	// Starting collecting
	// Command to execute
	$result = $_API -> comm($_COLLECTOR['cmd']);
	
	// Sending the debug-info if it's required by second arg in cli
	if ($_DEBUG === true && $_DEBUG_COLL == $_COLLECTOR['name']) {
		var_dump($result);
	}
	
	if (empty($result)) {
		$_OUT[] = prom('mt_collector_error', $_ARR_COLL + array('error' => 'Device had sent empty response'), 1);
	} else {
		/* Algo:
		1) Checking if capsman is enabled
			1a) Checking if CAP-manager is enabled:
			
			Y: Getting registration table from capsman w/ fields:
				
		
		
		*/
	}
	
	// Collector scrape duration
	$_coll_end_time = microtime(true);
	$_coll_scrape = round($_coll_end_time - $_coll_start_time, 7) * 1000;
	
	$_OUT[] = prom(PREFIX.'_collector_scrape_duration', $_ARR_COLL, $_coll_scrape);
	$_OUT[] = PHP_EOL.PHP_EOL;
	unset($_ARR_COLL);
}