# OSPF neighbors metrics

These metrics contains OSPF-neighbors metrics

## Specific labels

| Name | Description | Example value |
| ---- | ----------- | ------------- |
| `instance` | Instance name | `default` |
| `area` | OSPF area name | `backbone` |
| `address` | Neighbor address | `10.101.0.1` |
| `router_id` | Neighbor Router ID | `10.255.255.2` |

## Metrics
| Name | Description | Example value |
| ---- | ----------- | ------------- |
| `mikrotik_ospf_neighbor_status` | Returns neighbor status | `1` if state is `Full` otherwise `0` |
| `mikrotik_ospf_neighbor_state_changes` | Returns count of state changes | `123` |
| `mikrotik_ospf_neighbor_adjacency` | Returns count of adjacency | `123456` |