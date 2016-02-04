# z-diet-post-to-slack

## Setup

- git clone
- [composer install](https://getcomposer.org/doc/00-intro.md)
- Put Path/To/app/config/config_production.php
```
# config_production.php
<?php
return [
    'callback_url' => [
        'authorize'   => 'http://{your domain or ip}/authorize', // ex. http://example.com/authorize
    ],
    'api_key' => 'Your app`s api key',
    'api_key_secret' => 'Your app`s api key secret',
    'slack_webhook_url' => 'Your slack webhook url',
    'timestamp_of_reference' => 1452265200,
];
```
- Setting cron
```
# samle
0 10 * * * /var/www/vhosts/z-diet/app/bin/console z-diet:post_to_slack >> /tmp/exec_log 2>> /tmp/exec_error_log
```
