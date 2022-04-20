# Basic interface metrics

This collector contains common interface metrics that are applied to all types of interfaces, but availability of some metrics may vary

## Specific labels

| Name | Description | Example value |
| ---- | ----------- | ------------- |
| `interface_name` | Name for this interface | `ether4` |
| `interface_type` | Type of this interface | `ether` |

## Metrics
Some of these metrics return their value with label `value`
| Name | Description | Example value |
| ---- | ----------- | ------------- |
| `mikrotik_interface_running` | Returns if interface is running or not | `0` or `1` |
| `mikrotik_interface_disabled` | Returns if interface is disabled or not | `0` if NOT disabled or `1` if it is |
| `mikrotik_interface_default_name` | Default name (e.g. after factory reset) as a label | `1` always if set |
| `mikrotik_interface_mtu` | Preset MTU | `1500` |
| `mikrotik_interface_actual_mtu` | Current MTU | `1500` |
| `mikrotik_interface_l2mtu` | L2 MTU | `1598` |
| `mikrotik_interface_max_l2mtu` | Max L2 MTU | `1598` |
| `mikrotik_interface_mac_address` | MAC-address as a label | `1` always if set |
| `mikrotik_interface_last_link_down_time` | Last link-down time in a unixtime | `1649816024` |
| `mikrotik_interface_last_link_up_time` | Last link-up time in a unixtime | `1649816024` |
| `mikrotik_interface_link_downs` | Link-down count | `5` |
| `mikrotik_interface_rx_byte` | Number of bytes received | `953753019887` |
| `mikrotik_interface_tx_byte` | Number of bytes transmitted | `202340847533` |
| `mikrotik_interface_rx_packet` | Number of packets received | `915489315` |
| `mikrotik_interface_tx_packet` | Number of packets transmitted | `612424347` |
| `mikrotik_interface_rx_drop` | Number of receive packets dropped | `0` |
| `mikrotik_interface_tx_drop` | Number of transmit packets dropped | `0` |
| `mikrotik_interface_tx_queue_drop` | Number of transmit packets dropped because of queues | `0` |
| `mikrotik_interface_rx_error` | Number of receive packets with errors | `0` |
| `mikrotik_interface_tx_error` | Number of transmit packets with errors | `0` |
| `mikrotik_interface_rx_byte` | Number of bytes received with FastPath enabled | `953753019887` |
| `mikrotik_interface_tx_byte` | Number of bytes transmitted with FastPath enabled | `202340847533` |
| `mikrotik_interface_rx_packet` | Number of packets received with FastPath enabled | `915489315` |
| `mikrotik_interface_tx_packet` | Number of packets transmitted with FastPath enabled | `612424347` |
| `mikrotik_interface_comment` | Comment for this interface as a label | `1` always if set |