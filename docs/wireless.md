# Wireless clients metrics

This collector contains common info about wireless-clients. It supports two modes:
1) Standalone router (or access-point)
2) CAPsMAN network

You don't need to set mode for device. It detects mode automatically:
- If CAP manager is enabled - all clients are counted from CAPsMAN registration table
- If it's not, collector is checking if CAP is enabled (i.e. device is added to existing CAPsMAN network) - if it is, *collector won't work - you are able to view registration table only on main CAPsMAN device*
- If it's not - collector is working with device as a standalone router (access-point) - all clients are counted from wireless registration table

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
| `mikrotik_wireless_client_uptime` | Connection uptime in seconds | `48567` |
| `mikrotik_wireless_client_rx_signal` | Received signal value | `-35` |
| `mikrotik_wireless_client_tx_packets` | Transmitted packets to this client | `248804` |
| `mikrotik_wireless_client_rx_packets` | Received packets from this client | `69188` |
| `mikrotik_wireless_client_tx_bytes` | Transmitted bytes to this client | `20459861` |
| `mikrotik_wireless_client_rx_bytes` | Received bytes from this client | `8062532` |