<?php
// Collector name
$_COLLECTOR['enable'] = 1;
$_COLLECTOR['name'] = 'interface';
$_COLLECTOR['cmd'] = '/interface/print';
if (checkCollector($_COLLECTOR['name'], $_COLLECTORS)) {
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
		if ($_COLLECTOR['enable'] == 1) {
			// Starting the collection
			
			// Just foreach every string excepting some fields
			// 1st foreach: list of interfaces
			foreach ($result as $key => $interface) {
				// 2nd foreach: separate interface - sending all excluding interface name, type (it will be added for every metric string) and .id (it smells like shit)
				foreach ($interface as $metric_name => $value) {
					if ($metric_name != 'name' && $metric_name != 'type' && $metric_name != '.id') {
						// replacing '-' and '.' with '_'
						$mt = str_replace('-', '_', str_replace('.', '_', $metric_name));
						
						// Replacing awful date-format
						if ($metric_name == 'last-link-up-time' OR $metric_name == 'last-link-down-time') {
							$value = $value = DateTime::createFromFormat('M/d/Y H:i:s', $value) -> getTimestamp();
						}
						// Checking if value is int or float; if it is - we're using it as a real value. Also checking if it's true/false and replacing it with 1/0
						if (is_numeric($value)) {
							$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_'.$mt, $_ARR_COLL + array('interface_name' => $interface['name'], 'interface_type' => $interface['type']), $value);
						} elseif ($value == 'true' OR $value == 'false') {
							$value = $value == 'true' ? 1 : 0;
							$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_'.$mt, $_ARR_COLL + array('interface_name' => $interface['name'], 'interface_type' => $interface['type']), $value);
						} else {
							// Otherwise - we're adding this as a label and value 1
							$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_'.$mt, $_ARR_COLL + array('interface_name' => $interface['name'], 'interface_type' => $interface['type'], 'value' => $value), 1);
						}
					}
				}
				// Checking if "comment" is set. If not - adding "none" comment (label) with value "0"
				if (empty($interface['comment'])) {
					$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_comment', $_ARR_COLL + array('interface_name' => $interface['name'], 'interface_type' => $interface['type'], 'value' => 'none'), 0);
				}
				// Adding string between interfaces
				$_OUT[] = PHP_EOL;
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