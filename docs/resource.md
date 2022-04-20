# Resource metrics

This collector contains common resource stats from `/system/resource`. Availability of some metrics may vary


## Metrics
Some of these metrics return their value with label `value`
| Name | Description | Example value |
| ---- | ----------- | ------------- |
| `mikrotik_resource_uptime` | Device uptime in seconds | `86120` |
| `mikrotik_resource_version` | RouterOS version (string) as a label | `1` always if set |
| `mikrotik_resource_build_time` | Build time as unixtime for this RouterOS version | `1640087585`
| `mikrotik_resource_factory_software` | RouterOS factory version (string) as a label | `1` always if set |
| `mikrotik_resource_free_memory` | Free RAM in bytes | `931385344` |
| `mikrotik_resource_total_memory` | Total RAM in bytes | `1073741824` |
| `mikrotik_resource_cpu` | CPU common name as a label | `ARMv7` |
| `mikrotik_resource_cpu_count` | CPU sockets / cores | `4` |
| `mikrotik_resource_cpu_frequency` | CPU frequency in MHz | `448` |
| `mikrotik_resource_cpu_load` | CPU utilization in percents | `20%` |
| `mikrotik_resource_free_hdd_space` | Free disk space | `2150400` |
| `mikrotik_resource_total_hdd_space` | Total disk space | `15990784` |
| `mikrotik_resource_write_sect_since_reboot` | Written sectors since last reboot | `1071513` |
| `mikrotik_resource_write_sect_total` | Written sectors total | `1071513` |
| `mikrotik_resource_bad_blocks` | Bad blocks in percent (not sure about percent) | `0` |
| `mikrotik_resource_architecture_name` | CPU architecture name as a label | `arm` |
| `mikrotik_resource_board_name` | Board name (a.k.a model) | `hAP ac^2` |
| `mikrotik_resource_platform` | Platform name | `Mikrotik` |