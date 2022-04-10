<?php
// Collector name
$_COLLECTOR['enable'] = 1;
$_COLLECTOR['name'] = 'ospf';
$_COLLECTOR['cmd'] = '/routing/ospf/neighbor';
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
		foreach ($result as $key => $neighbor) {
			// We're setting 3 metrics:
			// _ospf_neighbor_status
			// If state is "Full" output result is 1, otherwise it's 0 (neighbor is down/init/etc.)
			// _ospf_neighbor_state_changes
			// _ospf_neighbor_adjacency
			// with labels instance, area, address, router-id

			// Forming labels-list:
			$labels = array('instance' => $neighbor['instance'], 'area' => $neighbor['area'], 'address' => $neighbor['address'], 'router_id' => $neighbor['router-id']);
			// Checking what value will be on status:
			$value = $neighbor['state'] == 'Full' ? 1 : 0;
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_neighbor_status', $_ARR_COLL + $labels, $value);
			// state-changes
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_neighbor_state_changes', $_ARR_COLL + $labels, $neighbor['state-changes']);
			// Convert awful mikrotik-date to seconds:
			$value = mikrotik_time($neighbor['adjacency']);
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_neighbor_adjacency', $_ARR_COLL + $labels, $value);
		}
	}
	
	// Collector scrape duration
	$_coll_end_time = microtime(true);
	$_coll_scrape = round($_coll_end_time - $_coll_start_time, 7) * 1000;
	
	$_OUT[] = prom(PREFIX.'_collector_scrape_duration', $_ARR_COLL, $_coll_scrape);
	$_OUT[] = PHP_EOL.PHP_EOL;
	unset($_ARR_COLL);
}