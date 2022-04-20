# Ethernet metrics

This collector contains all metrics from ethernet & sfp interfaces. Availability of metrics may vary.

## Specific labels

| Name | Description | Example value |
| ---- | ----------- | ------------- |
| `interface` | Name of wlan or cap interface | `wlan1`, `5G-Home-W2-1` |
| `ssid` | SSID for this client | `ASUS-5G` |
| `mac_address` | This client MAC-address | `AA:BB:CC:DD:EE:FF` |


## Metrics
Some of these metrics return their value with label `value`
| Name | Description | Example value |
| ---- | ----------- | ------------- |
| `mikrotik_int_ethernet_running` | Returns if interface is running or not | `0` or `1` |
| `mikrotik_int_ethernet_slave` | Returns if interface is slave (e.g. slave-port or in a bridge) or not | `0` or `1` |
| `mikrotik_int_ethernet_disabled` | Returns if interface is disabled or not | `0` if NOT disabled or `1` if it is |
| `mikrotik_int_ethernet_default_name` | Default name (e.g. after factory reset) as a label | `1` always if set |
| `mikrotik_int_ethernet_mtu` | Preset MTU | `1500` |
| `mikrotik_int_ethernet_actual_mtu` | Current MTU | `1500` |
| `mikrotik_int_ethernet_l2mtu` | L2 MTU | `1598` |
| `mikrotik_int_ethernet_max_l2mtu` | Max L2 MTU | `1598` |
| `mikrotik_int_ethernet_mac_address` | MAC-address as a label | `1` always if set |
| `mikrotik_int_ethernet_orig_mac_address` | Predefined MAC-address as a label | `1` always if set |
| `mikrotik_int_ethernet_arp` | ARP status (enabled or not) | `1` |
| `mikrotik_int_ethernet_arp_timeout` | ARP timeout (as a label `auto` if is not configured) | `auto` or some kind of value (not tested) |
| `mikrotik_int_ethernet_loop_protect` | Status of loop-protect (set) | `1` if enabled or default, `0` if disabled |
| `mikrotik_int_ethernet_loop_protect_status` | Actual status of loop-protect (set) | `1` if enabled or default, `0` if disabled |
| `mikrotik_int_ethernet_loop_protect_send_interval` | Sending loop-detect packets interval (in seconds) | `5` |
| `mikrotik_int_ethernet_loop_protect_disable_time` | Interval when interface will stay disabled if loop occures (in seconds) | `300` |
| `mikrotik_int_ethernet_auto_negotiation` | Auto Negotiation status | `1` or `0` |
| `mikrotik_int_ethernet_advertise` | Advertise list as a label, comma-separated | `1` always if set |
| `mikrotik_int_ethernet_full_duplex` | Full-duplex status | `1` or `0` |
| `mikrotik_int_ethernet_tx_flow_control` | Flow-control status for transmit-packets | `1` or `0` |
| `mikrotik_int_ethernet_rx_flow_control` | Flow-control status for received-packets | `1` or `0` |
| `mikrotik_int_ethernet_speed` | Actual physical link speed | `10`, `100`, `1000`, `2500` (not tested), `5000` (n.t.), `10000`, `25000` (n.t.), `40000` (n.t.) respectively for `10M`, `100M`, `1G`, `2.5G`, `5G`, `10G`, `25G`, `40G` |
| `mikrotik_int_ethernet_bandwidth` | RX/TX bandwith (set) as a label | Currently returns only `unlimited/unlimited` |
| `mikrotik_int_ethernet_switch` | Switch name as a label | `switch1` |
| `mikrotik_int_ethernet_driver_rx_byte` | Some kind (???????) of received bytes | `88254385` |
| `mikrotik_int_ethernet_driver_tx_byte` | Some kind (???????) of transmit bytes | `19587926` |
| `mikrotik_int_ethernet_driver_rx_packet` | Some kind (???????) of received packets | `206617133` |
| `mikrotik_int_ethernet_driver_tx_packet` | Some kind (???????) of transmit packets | `396592933` |
| `mikrotik_int_ethernet_poe_out` | PoE status (set) | `1` if auto or forced on, `0` if off |
| `mikrotik_int_ethernet_poe_priority` | PoE priority | `10` |
| `mikrotik_int_ethernet_power_cycle_ping_enabled` | Status of power cycle ping | `1` or `0` |
| `mikrotik_int_ethernet_power_cycle_ping_address` | Power cycle ping address as a value | `1` always if set |
| `mikrotik_int_ethernet_power_cycle_ping_timeout` | Power cycle ping timeout in seconds | `120` |
| `mikrotik_int_ethernet_power_cycle_interval` | Power cycle interval in seconds | `120` |
| `mikrotik_int_ethernet_sfp_rate_select` | SFP module rate-select as a label | `high`, `low` |
| `mikrotik_int_ethernet_fec_mode` | SFP FEC mode | `auto`, `fec74` |
| `mikrotik_int_ethernet_sfp_shutdown_temperature` | SFP disabling temperature (set), degrees in temperature | `auto`, `fec74` |
| `mikrotik_int_ethernet_last_link_down_time` | Last link-down time in a unixtime | `1649816024` |
| `mikrotik_int_ethernet_last_link_up_time` | Last link-up time in a unixtime | `1649816024` |
| `mikrotik_int_ethernet_link_downs` | Link-down count | `5` |
| `mikrotik_int_ethernet_rx_byte` | Number of bytes received | `953753019887` |
| `mikrotik_int_ethernet_tx_byte` | Number of bytes transmitted | `202340847533` |
| `mikrotik_int_ethernet_rx_packet` | Number of packets received | `915489315` |
| `mikrotik_int_ethernet_tx_packet` | Number of packets transmitted | `612424347` |
| `mikrotik_int_ethernet_rx_drop` | Number of receive packets dropped | `0` |
| `mikrotik_int_ethernet_tx_drop` | Number of transmit packets dropped | `0` |
| `mikrotik_int_ethernet_tx_queue_drop` | Number of transmit packets dropped because of queues | `0` |
| `mikrotik_int_ethernet_rx_error` | Number of receive packets with errors | `0` |
| `mikrotik_int_ethernet_tx_error` | Number of transmit packets with errors | `0` |
| `mikrotik_int_ethernet_rx_byte` | Number of bytes received with FastPath enabled | `953753019887` |
| `mikrotik_int_ethernet_tx_byte` | Number of bytes transmitted with FastPath enabled | `202340847533` |
| `mikrotik_int_ethernet_rx_packet` | Number of packets received with FastPath enabled | `915489315` |
| `mikrotik_int_ethernet_tx_packet` | Number of packets transmitted with FastPath enabled | `612424347` |
| `mikrotik_int_ethernet_tx_rx_64` | Count of transmitted & received packets with size 64 bytes | `123456` |
| `mikrotik_int_ethernet_tx_rx_65_127` | Count of transmitted & received packets with size 65-127 bytes | `123456` |
| `mikrotik_int_ethernet_tx_rx_128_255` | Count of transmitted & received packets with size 128-255 bytes | `123456` |
| `mikrotik_int_ethernet_tx_rx_256_511` | Count of transmitted & received packets with size 256-511 bytes | `123456` |
| `mikrotik_int_ethernet_tx_rx_512_1023` | Count of transmitted & received packets with size 512-1023 bytes | `123456` |
| `mikrotik_int_ethernet_tx_rx_1024_1518` | Count of transmitted & received packets with size 1024-1528 bytes | `123456` |
| `mikrotik_int_ethernet_rx_unicast` | Received unicast-packets | `1755908` |
| `mikrotik_int_ethernet_tx_unicast` | Transmitted unicast-packets | `1234567` |
| `mikrotik_int_ethernet_rx_broadcast` | Received broadcast-packets | `755908` |
| `mikrotik_int_ethernet_tx_broadcast` | Transmitted broadcast-packets | `234567` |
| `mikrotik_int_ethernet_rx_multicast` | Transmitted multicast-packets | `3551640` |
| `mikrotik_int_ethernet_tx_multicast` | Transmitted multicast-packets | `4356363` |
| `mikrotik_int_ethernet_rx_pause` | Number of received paused packets | `0` |
| `mikrotik_int_ethernet_tx_pause` | Number of transmitted paused packets | `0` |
| `mikrotik_int_ethernet_rx_fcs_error` | Number of received packets with FCS errors | `0` |
| `mikrotik_int_ethernet_rx_fragment` | Number of received packets with fragmentation errors | `0` |
| `mikrotik_int_ethernet_rx_unknown_op` | _Unknown_ | `0` |
| `mikrotik_int_ethernet_rx_code_error` | _Unknown_ | `0` |
| `mikrotik_int_ethernet_rx_jabber` | _Unknown_ | `0` |
| `mikrotik_int_ethernet_rx_error_events` | _Unknown_ | `0` |
| `mikrotik_int_ethernet_tx_collision` | Number of transmitted packets with collisions | `0` |
| `mikrotik_int_ethernet_tx_excessive_collision` | Number of transmitted packets with excessive collisions | `0` |
| `mikrotik_int_ethernet_tx_multiple_collision` | Number of transmitted packets with multiple collisions | `0` |
| `mikrotik_int_ethernet_tx_single_collision` | Number of transmitted packets with single collision | `0` |
| `mikrotik_int_ethernet_tx_late_collision` | Number of transmitted packets with late collision | `0` |
| `mikrotik_int_ethernet_tx_deferred` | Number of transmitted deferred packets | `0` |
| `mikrotik_int_ethernet_comment` | Comment for this interface as a label | `1` always if set |