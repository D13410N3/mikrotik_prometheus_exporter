<?php
// Collector name
$_COLLECTOR['enable'] = 1;
$_COLLECTOR['name'] = 'wireless';
if (checkCollector($_COLLECTOR['name'], $_COLLECTORS) && $_COLLECTOR['enable'] == 1) {
	$_coll_start_time = microtime(true);
	
	// Making new array
	$_ARR_COLL = $_ARR + array('collector' => $_COLLECTOR['name']);
	
	// Starting collecting
	/* Algo:
	1) Checking if capsman-manager is enabled
		Y: Getting registration table from capsman (interface=2G-Home-W1-1 ssid="ASUS" mac-address=C4:4F:33:EA:91:AB)
		N: 1a) Checking if wireless/cap is enabled
			Y: Capsman-client-device. Ignoring
			N: Standalone wireless-router. Getting registration table from wireless
	*/

	// 1)
	$cmd = '/caps-man/manager/print';
	$result = $_API -> comm($cmd);
	// Sending the debug-info if it's required by second arg in cli
	if ($_DEBUG === true && $_DEBUG_COLL == $_COLLECTOR['name']) {
		var_dump($result);
	}

	if ($result[0]['enabled'] == 'true') {
		// Y: Getting registration table from capsman:
		$cmd = '/caps-man/registration-table/print';
		$result = $_API -> comm($cmd);
		// Sending the debug-info if it's required by second arg in cli
		if ($_DEBUG === true && $_DEBUG_COLL == $_COLLECTOR['name']) {
			var_dump($result);
		}

		// Sending "wireless mode" as "capsman-manager"
		$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'], $_ARR_COLL + array('mode' => 'capsman-manager'), 1);

		// result is an array of arrays
		foreach ($result as $key => $w_client) {
			// Bind all labels to static:
			$labels = array('interface' => $w_client['interface'], 'ssid' => $w_client['ssid'], 'mac_address' => $w_client['mac-address']);

			// Getting uptime:
			$value = mikrotik_time($w_client['uptime']);
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_client_uptime', $_ARR_COLL + $labels, $value);

			// Getting signal:
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_client_signal', $_ARR_COLL + $labels, $w_client['signal']);

			// Getting packets:
			list($value_tx, $value_rx) = explode(',', $w_client['packets']);
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_client_tx_packets', $_ARR_COLL + $labels, $value_tx);
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_client_rx_packets', $_ARR_COLL + $labels, $value_rx);

			// Getting bytes:
			list($value_tx, $value_rx) = explode(',', $w_client['bytes']);
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_client_tx_bytes', $_ARR_COLL + $labels, $value_tx);
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_client_rx_bytes', $_ARR_COLL + $labels, $value_rx);
		}
	} else {
		// N: Checking if wireless-cap is enabled
		$cmd = '/interface/wireless/cap/print';
		$result = $_API -> comm($cmd);
		// Sending the debug-info if it's required by second arg in cli
		if ($_DEBUG === true && $_DEBUG_COLL == $_COLLECTOR['name']) {
			var_dump($result);
		}

		if ($result[0]['enabled'] == 'true') {
			// This is a capsman-client-device. Just notify about this
			// Sending "wireless mode" as "capsman-client"
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'], $_ARR_COLL + array('mode' => 'capsman-client'), 1);
		} else {
			// Sending "wireless mode" as "standalone"
			$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'], $_ARR_COLL + array('mode' => 'standalone'), 1);

			// Standalone wireless-router/access-point. Collecting in a usual way
			$cmd = '/interface/wireless/registration-table/print';
			$result = $_API -> comm($result);
			// Sending the debug-info if it's required by second arg in cli
			if ($_DEBUG === true && $_DEBUG_COLL == $_COLLECTOR['name']) {
				var_dump($result);
			}

			foreach ($result as $key => $w_client) {
				// Bind all labels to static:
				$labels = array('interface' => $w_client['interface'], 'ssid' => $w_client['ssid'], 'mac_address' => $w_client['mac-address']);

				// Getting uptime:
				$value = mikrotik_time($w_client['uptime']);
				$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_client_uptime', $_ARR_COLL + $labels, $value);

				// Getting signal:
				$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_client_signal', $_ARR_COLL + $labels, $w_client['signal']);

				// Getting packets:
				list($value_tx, $value_rx) = explode(',', $w_client['packets']);
				$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_client_tx_packets', $_ARR_COLL + $labels, $value_tx);
				$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_client_rx_packets', $_ARR_COLL + $labels, $value_rx);

				// Getting bytes:
				list($value_tx, $value_rx) = explode(',', $w_client['bytes']);
				$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_client_tx_bytes', $_ARR_COLL + $labels, $value_tx);
				$_OUT[] = prom(PREFIX.'_'.$_COLLECTOR['name'].'_client_rx_bytes', $_ARR_COLL + $labels, $value_rx);
			}
		}
	}
	
	// Collector scrape duration
	$_coll_end_time = microtime(true);
	$_coll_scrape = round($_coll_end_time - $_coll_start_time, 7) * 1000;
	
	$_OUT[] = prom(PREFIX.'_collector_scrape_duration', $_ARR_COLL, $_coll_scrape);
	$_OUT[] = PHP_EOL.PHP_EOL;
	unset($_ARR_COLL);
}