<?php
// Collector name
$_COLLECTOR['enable'] = 1;
$_COLLECTOR['name'] = 'wireguard';
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
		// Starting the collection
		// Saving fields: address, mac-address, server
		// 1st foreach: all clients to one client:
		foreach ($result as $key => $peer) {
			// We're going to add 3 metrics:
			// 1) rx
			// 2) tx
			// 3) last-handshake
			// 4) status
			// And save these labels in all of them:
			// interface, public-key, current-endpoint-address, current-endpoint-port, allowed-address, comment
			$label_names = array('interface', 'public-key', 'current-endpoint-address', 'current-endpoint-port', 'allowed-address', 'comment');
			
			$labels = array();
			foreach($label_names as $_key => $_value) {
				$_key = str_replace('-', '_', $_value);
				$labels[$_key] = $peer[$_value];
			}
			
			$value = array();
			$value['rx'] = mikrotik_traffic($peer['rx']);
			$value['tx'] = mikrotik_traffic($peer['tx']);
			$value['last_handshake'] = mikrotik_time($peer['last-handshake']);
			$value['status'] = $peer['disabled'] == 'false' ? 1 : 0;
			
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_status', $_ARR_COLL + $labels, $value['status']);
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_peer_rx', $_ARR_COLL + $labels, $value['rx']);
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_peer_tx', $_ARR_COLL + $labels, $value['tx']);
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_peer_last_handshake', $_ARR_COLL + $labels, $value['last_handshake']);
		}
	}
	
	// Collector scrape duration
	$_coll_end_time = microtime(true);
	$_coll_scrape = round($_coll_end_time - $_coll_start_time, 7) * 1000;
	
	$_OUT[] = prom(PREFIX.'_collector_scrape_duration', $_ARR_COLL, $_coll_scrape);
	$_OUT[] = PHP_EOL.PHP_EOL;
	unset($_ARR_COLL);
}