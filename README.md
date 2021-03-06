# mikrotik_prometheus_exporter
Simple Mikrotik-devices importer for Prometheus (under development)

## Features
- Devices segregation by location-label
- Each device can be set to use all collectors or only some of them
- All configuration are set in one file (see `db.sample.yml` for an example)

# Quick run with docker
- Create `db.yml` file by this [example](db.sample.yml)
- `docker run --name mikrotik_exporter -d -p 9180:80 -v /path/to/db.yml:/www/db.yml d13410n3/mikrotik_exporter:v1`

# Manual installation

## Requirements
It doesn't work as standalone-application - some kind of web-server required (tested with nginx).

Requirements - PHP 7+ with `curl` module, Web-server (my choice is nginx with php7.4-fpm). *All collectors are tested only with RouterOS version 7+.*


## Preparation
- Clone this repository to a web-server-dir (e.g. `/var/html`)
- Enable API (not SSL-api) on your Mikrotik-device
- Add read-only user
- (recommended) disable logging for topics `info`, `account`, otherwise you'll get a lot of login/logout messages to your log
- Create & fill file `db.yml` with credentials from your devices. Use [db.sample.yml](db.sample.yml) as an example. *Don't forget to restrict direct access to this file!*


## Prometheus configuration
### Scraping example
```
  - job_name: mikrotik
    static_configs:
      -
        targets:
        - '10.100.0.1/Home-GW'
        - '10.100.0.6/Home-CHR'

        labels:
          __tmp_exporter: 10.100.2.7:9180
    relabel_configs:
      -
        source_labels: [__address__]
        regex: "^(.*)/(.*)$"
        target_label: __metrics_path__
        replacement: /new/metrics/${1}
      -
        source_labels: [__address__]
        regex: "^(.*)/(.*)$"
        target_label: instance_ip
        replacement: ${1}
      -
        source_labels: [__address__]
        regex: "^(.*)/(.*)$"
        target_label: hostname
        replacement: ${2}
      -
        source_labels: [__tmp_exporter]
        regex: "(.*)"
        target_label: __address__
        replacement: ${1}
```
This config will scrape two Mikrotik-devices with addresses `10.100.0.1` and `10.100.0.6` using URLs:
- `http://10.100.2.7:9180/new/metrics/10.100.0.1`
- `http://10.100.2.7:9180/new/metrics/10.100.0.6`
... every 30 seconds with 10 seconds timeout

About `targets` format: I'm using it to predefine device hostname with relabeling, because I can't use any kind of SD. If you don't need it - replace with your own. You can also add labels to metrics-output - view [this commit](https://github.com/D13410N3/mikrotik_prometheus_exporter/commit/bc42f2e5771decd0cab02ec18d9f5e3b63616ee7) to see how to revert it.


### Alerting example:
[rules_mt.yml](rules_mt.yml)


## Web-server configuration
Nginx example (real, used by me):
```
server
{
        listen 9180 default;
        set $doc_root '/var/www/000-default-mikrotik';
        root $doc_root;
        index index.php;


        location ~* (metrics|index)\.php$ {
                if (!-f "$document_root$fastcgi_script_name") {
                        return 404;
                }

                fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
                fastcgi_index index.php;
                include fastcgi_params;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                fastcgi_ignore_client_abort on;
        }

        location ~*  {
                deny all;
        }

        rewrite "^/metrics/(.*?)$" /metrics.php?ip=$1;
        access_log /var/log/nginx/mikrotik.access.log;
        error_log /var/log/nginx/mikrotik.error.log;
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


# Feedback
Feel free to contact me via issues, Telegram or email from Github-profile
