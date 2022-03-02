# iCloud Private Relay IP Checker

[https://dev.ksn.cloud/icloudpr/](https://dev.ksn.cloud/icloudpr/)

## Usage

```console
$ curl "https://dev.ksn.cloud/icloudpr/?ip=192.0.2.0"
{
    "status": "error",
    "msg": "[invalid] Only check ipv4 addresses."
}

$ curl "https://dev.ksn.cloud/icloudpr/?ip=146.75.223.33"
{
    "status": "matched",
    "ip": "146.75.223.33",
    "network": "146.75.223.32/31",
    "country": "US",
    "region": "US-NJ",
    "regionName": "PISCATAWAY"
}
```

## Reference
https://developer.apple.com/support/prepare-your-network-for-icloud-private-relay
