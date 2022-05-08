<?php
// Collector name
$_COLLECTOR['enable'] = 1;
$_COLLECTOR['name'] = 'routerboard';
$_COLLECTOR['cmd'] = '/system/routerboard/print';
if (checkCollector($_COLLECTOR['name'], $_COLLECTORS)) {
	$_coll_start_time = microtime(true);
	
	// Making new array
	$_ARR_COLL = $_ARR + array('collector' => $_COLLECTOR['name']);
	
	// Starting collecting
	// Command to execute
	$result = $_API -> comm($_COLLECTOR['cmd']);
	$res = $result[0];
	
	// Sending the debug-info if it's required by second arg in cli
	if ($_DEBUG === true && $_DEBUG_COLL == $_COLLECTOR['name']) {
		var_dump($res);
	}
	
	if (empty($res)) {
		$_OUT[] = prom(PREFIX.'_collector_error', $_ARR_COLL + array('error' => 'Device had sent empty response'), 1);
	} else {
		if ($_COLLECTOR['enable'] == 1) {
			// Just foreach every string excepting some fields
			foreach ($res as $metric_name => $value) {
				// replacing '-' and '.' with '_'
				$mt = str_replace('-', '_', str_replace('.', '_', $metric_name));
				
				// Including version number as integer
				if ($metric_name == 'factory-firmware' | $metric_name == 'current-firmware' | $metric_name == 'upgrade-firmware') {
					$value = version_convertor($value);
				}
				
				// Checking if value is int or float; if it is - we're using it as a real value
				if (is_numeric($value)) {
					$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_'.$mt, $_ARR_COLL, $value);
				} else {
					// Otherwise - we're adding this as a label and value 1
					$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_'.$mt, $_ARR_COLL + array('value' => $value), 1);
				}
			}
		} elseif ($_DEBUG === true) {
			var_dump($result);
		}
	}
	
	// Collector scrape duration
	$_coll_end_time = microtime(true);
	$_coll_scrape = round($_coll_end_time - $_coll_start_time, 7) * 1000;
	
	$_OUT[] = prom(PREFIX.'_collector_scrape_duration', $_ARR_COLL, $_coll_scrape);
	$_OUT[] = PHP_EOL.PHP_EOL;
	unset($_ARR_COLL);
}