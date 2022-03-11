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
		$_OUT[] = prom('mt_collector_error', $_ARR_COLL + array('error' => 'Device had sent empty response'), 1);
	} else {
		// Starting the collection
		// 1st foreach: all clients to one client:
		foreach ($result as $key => $lease) {
			// 2nd foreach: rewrite labels and values
			$labels = array();
			foreach ($lease as $option_name => $option_value) {
				// Excluding 'status' (it will be value)
				if ($option_name != 'status') {
					// replacing '.' and '-' with '_' for label name 
					$option_name = str_replace('-', '_', str_replace('.', '_', $option_name));
					// detecting true-false values & replacing them with 1-0
					if ($option_value == 'true' OR $option_value == 'false') {
						$option_value = $option_value == 'true' ? 1 : 0;
					}
					// working with awful date
					if ($option_name == 'last_seen' OR $option_name == 'expires_after') {
						$option_value = mikrotik_time($option_value);
					}
					$labels[$option_name] = $option_value;
				}
			}
			// Setting up 1 or 0 value if device is bound or not
			$value = $lease['status'] == 'bound' ? 1 : 0;
			// All data saved - let's create prom-string
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