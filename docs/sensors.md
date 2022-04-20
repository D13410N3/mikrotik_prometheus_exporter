# Sensors metrics

This collector contains sensors value from `/system/health`. A lot of devices doesn't contain any type of sensors. Collector has only one metric - `mikrotik_sensors` with values from device-sensors

## Specific labels

| Name | Description | Example value |
| ---- | ----------- | ------------- |
| `sensor` | Name for this sensor | `voltage` |