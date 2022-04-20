# DHCP-lease metrics

This collectors contains only one metric - `mikrotik_dhcp_lease` with value `1` or `0`. It means if lease-status is active (bound) or not

## Specific labels
| Name | Description | Example value |
| ---- | ----------- | ------------- |
| address | DHCP-client IP-address | `10.100.0.100` |
| mac_address | DHCP-client MAC address | `AA:BB:CC:DD:EE:FF` |
| server | Name of DHCP-server | `dhcp1` |
| client_hostname | Hostname (or FQDN) of client, but filtered with regexp `[^0-9a-zA-Z\.\-\_\s]` | `MY-PC` |
| comment | Comment for this client | `My own PC` |