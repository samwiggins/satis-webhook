# Satis Webhook

This package allows you to easily setup a post-receive hook for your Satis on services like Github.

## Getting started

First of all, you need to configure some basic information about where your Satis files are stored.

```sh
cp config.yml.dist config.yml
```

- **bin**: The location of your *bin/satis* file. Default: ```bin/satis```.
- **json**: The location of your *satis.json* file. Default: ```satis.json```.
- **webroot**: The location of your satis webroot, where packages.json is going to be dumped. Default: ```web/```.
- **user**: The location of your *bin/satis* file. This parameter is optional and default to ```null```.
- **authorized_ips**: The IP address list allowed to access the page. This parameter is optional and default to the current GitHub hook servers (```[204.232.175.64/27, 192.30.252.0/22]```).

Make your ```webhook.php``` file accessible, for example:

```
http://satis.yourcompany.com/webhook.php
```
