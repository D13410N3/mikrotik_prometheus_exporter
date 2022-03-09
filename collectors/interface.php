<?php
// Collector name
$_COLLECTOR['enable'] = 1;
$_COLLECTOR['name'] = 'interface';
$_COLLECTOR['cmd'] = '/interface/print';
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
		/*
		    [".id"]=>
			string(2) "*E"
			["name"]=>
			string(5) "local"
			["type"]=>
			string(6) "bridge"
			["mtu"]=>
			string(4) "auto"
			["actual-mtu"]=>
			string(4) "1500"
			["l2mtu"]=>
			string(4) "1592"
			["mac-address"]=>
			string(17) "C4:AD:34:D3:22:C8"
			["last-link-up-time"]=>
			string(20) "jan/15/2022 00:23:08"
			["link-downs"]=>
			string(1) "0"
			["rx-byte"]=>
			string(12) "825353547951"
			["tx-byte"]=>
			string(14) "10519523114081"
			["rx-packet"]=>
			string(10) "2478980033"
			["tx-packet"]=>
			string(10) "9269898847"
			["rx-drop"]=>
			string(1) "0"
			["tx-drop"]=>
			string(1) "0"
			["tx-queue-drop"]=>
			string(1) "0"
			["rx-error"]=>
			string(1) "0"
			["tx-error"]=>
			string(1) "0"
			["fp-rx-byte"]=>
			string(12) "819981214692"
			["fp-tx-byte"]=>
			string(3) "408"
			["fp-rx-packet"]=>
			string(10) "2433839380"
			["fp-tx-packet"]=>
			string(1) "4"
			["running"]=>
			string(4) "true"
			["disabled"]=>
			string(5) "false"
		*/
		
		// Just foreach every string excepting some fields
		// 1st foreach: list of interfaces
		foreach ($result as $key => $interface) {
			// 2nd foreach: separate interface - sending all excluding interface name (it will be added for every metric string) and it (it smells like shit)
			foreach ($interface as $metric_name => $value) {
				if ($metric_name != 'name' && $metric_name != '.id') {
					// replacing '-' with '_'
					$mt = str_replace('-', '_', $metric_name);
					
					// Replacing awful date-format
					if ($metric_name == 'last-link-up-time' OR $metric_name == 'last-link-down-time') {
						$value = $value = DateTime::createFromFormat('M/d/Y H:i:s', $value) -> getTimestamp();
					}
					// Checking if value is int or float; if it is - we're using it as a real value. Also checking if it's true/false and replacing it with 1/0
					if (is_numeric($value)) {
						$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_'.$mt, $_ARR_COLL + array('interface_name' => $interface['name']), $value);
					} elseif ($value == 'true' OR $value == 'false') {
						$value = $value == 'true' ? 1 : 0;
						$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_'.$mt, $_ARR_COLL + array('interface_name' => $interface['name']), $value);
					} else {
						// Otherwise - we're adding this as a label and value 1
						$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_'.$mt, $_ARR_COLL + array('interface_name' => $interface['name'], 'value' => $value), 1);
					}
				}
			}
			// Adding string between interfaces
			$_OUT[] = PHP_EOL;
		}
	}
	
	// Collector scrape duration
	$_coll_end_time = microtime(true);
	$_coll_scrape = round($_coll_end_time - $_coll_start_time, 7) * 1000;
	
	$_OUT[] = prom(PREFIX.'_collector_scrape_duration', $_ARR_COLL, $_coll_scrape);
	$_OUT[] = PHP_EOL.PHP_EOL;
	unset($_ARR_COLL);
}