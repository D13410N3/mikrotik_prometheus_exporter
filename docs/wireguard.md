# Wireguard peers metrics

This collector contains common info about Wireguard-peers

## Specific labels

| Name | Description | Example value |
| ---- | ----------- | ------------- |
| `interface` | Interface of this peer | `wg0` |
| `public_key` | Public key of a peer | `EhS93krpN4blAEFQXtGewzCXPisCcqqrF8Vv+vt1iQw=` |
| `current_endpoint_address` | Current address of this peer | `1.2.3.4` |
| `current_endpoint_port` | Current port of this peer | `4321` |
| `allowed_address` | List of allowed-address for this peer (comma-separated) | `0.0.0.0/0` |
| `comment` | Comment | `My Phone` |

## Metrics
Some of these metrics return their value with label `value`
| Name | Description | Example value |
| ---- | ----------- | ------------- |
| `mikrotik_wireguard_status` | Returns if this peer is enabled or not. This metric DOES NOT show actual tunnel-state | `1` or `0` |
| `mikrotik_wireguard_peer_rx` | Bytes received from peer | `501030024` |
| `mikrotik_wireguard_peer_rx` | Bytes transmitted to peer | `53821200` |
| `mikrotik_wireguard_peer_last_handshake` | Last handshake, seconds ago, rounded by 60 (1 minute). That's why API may return `0` even it's not `0`. Real `0` is only for tunnels that are not active since last boot time | `600` |