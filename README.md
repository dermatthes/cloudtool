# cloudtool
PHP Library to generate config files.

- 


## Install

```
sudo composer global require cloud/cloudtool
```


## Configuration Management

Create configuration files from templates on the fly.

`cloudtool` will search for `*.ctt`- files (cloud-tool-template) and
interpret them.

```
{template target="/etc/apache/sites-enabled/001_vhost.conf" owner="root" group="root" mode="0755"}
{template onupdate="service apache2 restart"}
{env name="CLOUD_CONFIG_URL" > configUrl}

{fetch url=configUrl enc="JSON" > config}


{for host in config.hosts}
    <VirtualHost *:80>
        ServerName {= host.name}
        DocumentRoot {= host.docRoot}
    </VirtualHost>
{/for}
```


