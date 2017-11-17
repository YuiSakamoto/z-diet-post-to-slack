# z-diet-post-to-slack

This application using the API of withings, provides the ability to post a measurement result to the slack.

As a comparison reference timestamp settings of config_production.php of `timestamp_of_reference`, and the first data in the comparison reference timestamp later, we compared the latest data at the time of batch execution, and post the results to the slack.

This application does not use the database. Data files that are generated are stored in the Path/To/tmp dir.

Environment in which it was confirmed that to work properly only PHP7.0.2.

## Post example

```
z-diet BOT [10:00] 
【今日の結果】
1位:Brian => 90.00%
2位:James => 91.11%
3位:Lucas => 92.22%
4位:Jackson => 93.33%
5位:Emma => 94.44%
6位:Olivia => 95.55%
7位:Charlotte => 96.66%
8位:Chloe => 97.77%
9位:Harper => 98.88%
```

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

## See

- http://oauth.withings.com/api
- https://api.slack.com/incoming-webhooks
