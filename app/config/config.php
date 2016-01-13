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
    ],
    'callback_url' => [
        'authorize'   => 'http://localhost:8000/authorize',
    ],
    'api_key' => '%API_KEY%',
    'api_secret' => '%API_SECRET%',
    'logger' => [
        'path' => dirname(dirname(__DIR__)) . '/tmp/z-diet.log',
        'level' => \Monolog\Logger::WARNING,
    ],
];