# mikrotik_prometheus_exporter
Simple Mikrotik-devices importer for Prometheus (under development)

## Features
- Devices segregation by location-label
- Each device can be set to use all collectors or only some of them
- All configuration are set in one file (see `db.sample.yml` for an example)

## db.yml example
View file (db.sample.yml)[db.sample.yml]

## Quick run with docker
- Create `db.yml` file
- `docker run --name mikrotik_exporter -d -p 9937:80 -v /path/to/db.yml:/www/db.yml d13410n3/mikrotik_exporter:v1`

## Requirements
It doesn't work as standalone-application - some kind of web-server required (tested with nginx).

Requirements - PHP 7+ with `curl` module, Web-server. All collectors are tested only with RouterOS version 7


## Preparation
- Clone this repository to a web-server-dir (e.g. `/var/html`)
- Enable API (not SSL-api) on your Mikrotik-device
- Add read-only user
- (recommended) disable logging for topics `info`, `account`, otherwise you'll get a lot of login/logout messages to your log
- Create & fill file `db.yml` with credentials from your devices. Use `db.sample.yml` as an example. *Don't forget to restrict direct access to this file!*


## Prometheus configuration
Scraping example:
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

Alerting example:

[rules_mt.yml](rules_mt.yml)


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

Each collector contains label `collector_name` and some additional metrics

| Name | Description | Output value type |
| ---- | ----------- | ----------------- |
| `mikrotik_collector_scrape_duration` | Return scrape time for this collector | Time interval in msec |
| `mikrotik_collector_error` | Returns error text as a label `error` | `1` |

## Basic collectors

| Name | Description |
| ---- | ----------- |
| [dhcp_lease](docs/dhcp_lease.md) | DHCP clients |
| [interface](docs/interface.md) | Summary stats for all interfaces (common values) |
| [ospf](docs/ospf.md) | OSPF neighbors stats |
| [resource](docs/resource.md) | Information from section "resources" (CPU, RAM, OS version etc.) |
| [sensors](docs/sensors.md) | Sensors (e.g. input voltage, temperature) |
| [wireguard](docs/wireguard.md) | Wireguard peers information |
| [wireless](docs/wireguard.md) | Wireless clients (support standalone & capsman modes) |

## Interfaces-group collectors

| Name | Description |
| ---- | ----------- |
| [int_ethernet](docs/int_ethernet.md) | Extended Ethernet stats (including sfp/sfp-sfpplus ports) |


## Debug

You can run exporter via cli using this syntax:
- `php metrics.php 10.100.0.1` - returns default metrics output
- `php metrics.php 10.100.0.1 interface` - return var_dump() of Mikrotik API response and print default metrics output
