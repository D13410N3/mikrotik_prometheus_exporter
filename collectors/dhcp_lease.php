<?php
// Collector name
$_COLLECTOR['enable'] = 1;
$_COLLECTOR['name'] = 'dhcp_lease';
$_COLLECTOR['cmd'] = '/ip/dhcp-server/lease/print';
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
		$_OUT[] = prom(PREFIX.'_collector_error', $_ARR_COLL + array('error' => 'Device had sent empty response'), 1);
	} else {
		// Starting the collection
		// Saving fields: address, mac-address, server
		// 1st foreach: all clients to one client:
		foreach ($result as $key => $lease) {
			// labels to add 
			$labels = array('address' => $lease['address'], 'mac_address' => $lease['mac-address'], 'server' => $lease['server']);
			// this labels can be empty
			$labels['client_hostname'] = isset($lease['host-name']) ? preg_replace('#[^0-9a-zA-Z\.\-\_\s]#', '', $lease['host-name']) : '';
			$labels['comment'] = isset($lease['comment']) ?  preg_replace('#[^0-9a-zA-Z\.\-\_\s]#', '', $lease['comment']) : '';
			$value = $lease['status'] == 'bound' ? 1 : 0;
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'], $_ARR_COLL + $labels, $value);
		}
	}
	
	// Collector scrape duration
	$_coll_end_time = microtime(true);
	$_coll_scrape = round($_coll_end_time - $_coll_start_time, 7) * 1000;
	
	$_OUT[] = prom(PREFIX.'_collector_scrape_duration', $_ARR_COLL, $_coll_scrape);
	$_OUT[] = PHP_EOL.PHP_EOL;
	unset($_ARR_COLL);
}