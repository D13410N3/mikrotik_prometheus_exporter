# DHCP-lease metrics

This collectors contains only one metric - `mikrotik_dhcp_lease` with value `1` or `0`. It means if lease-status is active (bound) or not

## Specific labels
| Name | Description |
| ---- | ----------- |
| address | DHCP-client IP-address |
| mac_address | DHCP-client MAC address |
| server | Name of DHCP-server |
| client_hostname | Hostname of client, but filtered with regexp `[^0-9a-zA-Z\.\-\_\s]` |
| comment | Comment for this client |