<?php
// Collector name
$_COLLECTOR['enable'] = 1;
$_COLLECTOR['name'] = 'int_ethernet';
$_COLLECTOR['cmd'] = '/interface/ethernet/print';
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
			// Collecting interface-names as an array - this will help us to use "monitor" function. We need it to get actual link speed
			$_names = array();
			foreach ($result as $key => $interface) {
				$_names[] = $interface['name'];
				// 2nd foreach: separate interface - sending all excluding interface name, type (it will be added for every metric string) and .id (it smells like shit)
				// We need to set something to comment if it's not set. At least, until I'll find JOIN LEFT solution
				// if (!isset($interface['comment'])) $interface['comment'] = '';
				
				foreach ($interface as $metric_name => $value) {
					if ($metric_name != 'name' && $metric_name != 'type' && $metric_name != '.id' && $metric_name != 'full-duplex') {
						// replacing '-' and '.' with '_'
						$mt = str_replace('-', '_', str_replace('.', '_', $metric_name));
						
						// Replacing awful date-format
						if ($metric_name == 'last-link-up-time' OR $metric_name == 'last-link-down-time') {
							$value = DateTime::createFromFormat('M/d/Y H:i:s', $value) -> getTimestamp();
						}
						
						// Replacing link_speed to a numeric-value
						if ($metric_name == 'speed') {
							$value = mikrotik_link_speed($value);
						}
						
						// Replacing awful time-interval value
						if ($metric_name == 'loop-protect-send-interval' OR $metric_name == 'loop-protect-disable-time') {
							$value = mikrotik_time($value);
						}
						
						// Replacing loop-protect and flow-control status
						if (in_array($metric_name, array('loop-protect', 'loop-protect-status', 'tx-flow-control', 'rx-flow-control'))) {
							switch($value) {
								case 'on':			$new_value = 1;			break;
								case 'off':			$new_value = 0;			break;
								case 'default':		$new_value = 1;			break;
								default:			$new_value = 1;			break;
							}
							
							$value = $new_value;
						}
						
						// Replacing PoE status
						if ($metric_name == 'poe-out') {
							switch($value) {
								case 'auto-on':		$new_value = 1;			break;
								case 'forced-on':	$new_value = 1;			break;
								case 'off':			$new_value = 0;			break;
							}
							
							$value = $new_value;
						}
						
						// Replacing power-cycle interval
						if ($metric_name == 'power-cycle-interval' OR $metric_name == 'power-cycle-ping-timeout') {
							$value = $value == 'none' ? '0' : mikrotik_time($value);
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
			
			// Collecting "monitor" stats to add actual speed & duplex
			$ether_int_list = implode(',', $_names);
			// die($ether_int_list);
			$cmd_monitor = '/interface/ethernet/monitor'.PHP_EOL.'=numbers='.$ether_int_list.PHP_EOL.'=once='.PHP_EOL.'=.proplist=name,status,rate,full-duplex';
			$result = $_API -> comm($cmd_monitor);
			if (empty($result)) {
				$_OUT[] = prom(PREFIX.'_collector_error', $_ARR_COLL + array('error' => 'Device had sent empty response (monitor)'), 1);
			} else {
				foreach ($result as $key => $value) {
					if ($value['status'] != 'no-link') {
						// link is ok, collecting data
						// Sometimes rate is not defined and "full-duplex" is "false". In my case it was on CHR with vmware
						if (!empty($value['rate'])) {
							// Actual speed
							$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_actual_speed', $_ARR_COLL + array('interface_name' => $value['name']), mikrotik_link_speed($value['rate']));
							// Duplex
							$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_full_duplex', $_ARR_COLL + array('interface_name' => $value['name']), ($value['full-duplex'] == 'true' ? 1 : 0));
						}
					}
				}
			}
				
			
		} elseif ($_DEBUG === true) {
			var_dump($result); die;
		}
	}
	
	// Collector scrape duration
	$_coll_end_time = microtime(true);
	$_coll_scrape = round($_coll_end_time - $_coll_start_time, 7) * 1000;
	
	$_OUT[] = prom(PREFIX.'_collector_scrape_duration', $_ARR_COLL, $_coll_scrape);
	$_OUT[] = PHP_EOL.PHP_EOL;
	unset($_ARR_COLL);
}