# mikrotik_prometheus_exporter
Simple Mikrotik-devices importer for Prometheus


# Requirements
It doesn't work as standalone-application - some kind of web-server required (tested with nginx).

Requirements - PHP 7+ with `curl` module, Web-server


# Preparation
- Clone this repository to a web-server-dir (e.g. `/var/html`)
- Enable API (not SSL-api) on your Mikrotik-device
- Add read-only user
- (recommended) disable logging for topics `info`, `account`, otherwise you'll get a lot of login/logout messages to your log
- Create & fill file `db.yml` with credentials from your devices. Use `db.sample.yml` as an example. *Don't forget to restrict direct access to this file!*


# Prometheus configuration
Example:
```
  - job_name: mikrotik
    scrape_interval: 30s
    scrape_timeout: 10s
    static_configs:
      -
        targets:
          - 10.100.0.1
          - 10.100.0.6
        labels:
          __tmp_exporter: localhost:1490

    relabel_configs:
      -
        source_labels: [__address__]
        regex: "(.*)"
        target_label: __metrics_path__
        replacement: /new/metrics/${1}
      -
        source_labels: [__tmp_exporter]
        regex: "(.*)"
        target_label: __address__
        replacement: ${1}
```


# Collectors
This list is not completed - development is still under process

## Basic

| Name | Description |
| ---- | ----------- |
| [dhcp_lease](md/dhcp_lease.md) | DHCP clients |
| [interface](md/interface.md) | Summary stats for all interfaces (common values) |
| [ospf](md/ospf.md) | OSPF neighbors stats |
| [resource](md/resource.md) | Information from section "resources" (CPU, RAM, OS version etc.) |
| [sensors](md/sensors.md) | Sensors (e.g. input voltage, temperature) |
| [wireguard](md/wireguard.md) | Wireguard peers information |
| [wireless](md/wireguard.md) | Wireless clients (support standalone & capsman modes) |

## Interfaces-group collectors

| Name | Description |
| ---- | ----------- |
| int_ethernet | Extended Ethernet stats (including sfp/sfp-sfpplus ports) |