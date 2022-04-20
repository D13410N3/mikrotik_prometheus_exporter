# mikrotik_prometheus_exporter
Simple Mikrotik-devices importer for Prometheus (under development)


## Requirements
It doesn't work as standalone-application - some kind of web-server required (tested with nginx).

Requirements - PHP 7+ with `curl` module, Web-server


## Preparation
- Clone this repository to a web-server-dir (e.g. `/var/html`)
- Enable API (not SSL-api) on your Mikrotik-device
- Add read-only user
- (recommended) disable logging for topics `info`, `account`, otherwise you'll get a lot of login/logout messages to your log
- Create & fill file `db.yml` with credentials from your devices. Use `db.sample.yml` as an example. *Don't forget to restrict direct access to this file!*


## Prometheus configuration
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
          __tmp_exporter: localhost:9180

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
This config will scrape two Mikrotik-devices with addresses `10.100.0.1` and `10.100.0.6` using URLs:
- `http://localhost:9180/new/metrics/10.100.0.1`
- `http://localhost:9180/new/metrics/10.100.0.6`
... every 30 seconds with 10 seconds timeout

## Web-server configuration
Nginx example:
```
server
{
        listen 1488 default;
        set $doc_root '/var/www/mikrotik_prometheus_exporter';
        include /etc/nginx/with-fcgi.conf; // This file contains common PHP-FPM directives
        rewrite "^/metrics$" /metrics.php;
        rewrite "^/new/metrics/(.*?)$" /new/metrics.php?ip=$1;
}
```



# Collectors
This list is not completed - development is still under process

## Prefix for metrics
You can set your own metrics prefix by edition `define('PREFIX', 'mikrotik');` in `init.php`. Examples contains `mikrotik` as a default prefix.

## Global

### Labels
All metrics contains these labels. They are taken from `db.yml`, not from device

| Label name | Description | Example value |
| ---------- | ----------- | ------------- |
| `ip` | Device IP | `10.100.0.1` |
| `hostname` | Device hostname | `Msk-R1` |
| `location` | Device location | `Moscow` |

### Basic metrics
These metrics are enabled for all added devices

| Metric name | Description | Output value type |
| ----------- | ----------- | -------------------- |
| `mikrotik_global_status` | Availability of device. Useless at this moment (if device is not enabled - die() function works | `0`/`1` |
| `mikrotik_global_scrape_duration` | Return scrape time for this device | Time interval in msec |
| `mikrotik_global_last_scrape_time` | Return unixtime of last scrape completion |

## Collectors

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
| [int_ethernet](md/int_ethernet.md) | Extended Ethernet stats (including sfp/sfp-sfpplus ports) |


Each collector contains label `collector_name` and some additional metrics

| Name | Description | Output value type |
| `mikrotik_collector_scrape_duration` | Return scrape time for this collector | Time interval in msec |
| `mikrotik_collector_error` | Returns error text as a label `error` | `1` |