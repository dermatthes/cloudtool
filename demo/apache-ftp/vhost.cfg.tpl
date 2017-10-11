{template target="/etc/apache/sites-enabled/001_vhost.conf"}
{template onupdate="service apache2 restart"}
{importFromEnv envName="CLOUD_CONFIG_URL" default="" > configUrl}

{fetch url=configUrl enc="JSON" > config}


{for host in config.hosts}
    <VirtualHost *:80>
        ServerName {= host.name}
        DocumentRoot {= host.docRoot}
    </VirtualHost>
{/for}