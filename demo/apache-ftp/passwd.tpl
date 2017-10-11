{template target="/etc/apache/sites-enabled/001_vhost.conf"}
{env name="CONF_POLL_URL" > pollUrl}
{fetch url=pollUrl enc="JSON" > config}

{include file="passwd.orig"}

{for curHost in config.hosts}
    {for passwd in curHost.passwd}
        {explode input=passwd delimiter=";" > curLine}
{=curLine.0}:{=curLine.1}:{=passwd.uid}:{=passwd.gid}::::{=curLine.3}:/bin/false
    {/for}
{/for}