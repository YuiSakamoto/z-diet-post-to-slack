<?php
/**
 * Created by IntelliJ IDEA.
 * User: yui_tang
 * Date: 1/12/16
 * Time: 19:44
 */
return [
    'request_url' => [
        'request_token'   => 'https://oauth.withings.com/account/request_token',
        'authorize'   => 'https://oauth.withings.com/account/authorize',
        'access_token'   => 'https://oauth.withings.com/account/access_token',
        'measure' => 'http://wbsapi.withings.net/measure',
    ],
    'callback_url' => [
        'authorize'   => 'http://localhost:8000/authorize',
    ],
    'api_key' => '',
    'api_key_secret' => '',
    'slack_webhook_url' => '',
    'timestamp_of_reference' => 1452265200,
    'logger' => [
        'path' => dirname(dirname(__DIR__)) . '/tmp/z-diet.log',
        'level' => \Monolog\Logger::WARNING,
    ],
];