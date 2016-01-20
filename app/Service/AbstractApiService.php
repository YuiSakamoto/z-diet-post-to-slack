<?php

namespace ZDiet\Service;

use Pimple\Container;
use GuzzleHttp\Client;

/**
 * Created by IntelliJ IDEA.
 * User: yui_tang
 * Date: 1/12/16
 * Time: 20:11
 */
class AbstractApiService
{

    const OAUTH_VERSION = '1.0';
    const OAUTH_SIGNATURE_METHOD = 'HMAC-SHA1';
    protected $api_key;
    protected $api_key_secret;
    protected $container;
    protected $oauth_token_secret_path;
    protected $client;

    public function __construct(Container $container, $tmp_dir_path)
    {
        $this->container = $container;
        $this->oauth_token_secret_path = $tmp_dir_path . '/oauth_token_secret';
        $this->user_tokens_dir_path = $tmp_dir_path . '/users';
        $this->api_key = $this->container['app.config']->get('api_key');
        $this->api_key_secret = $this->container['app.config']->get('api_key_secret');
        $this->client = new Client([
            'base_uri' => $this->container['app.config']->get('request_url.request_token'),
            'timeout' => 3.0,
        ]);
    }

    protected function createOauthNonce($length = 32)
    {
        return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
    }
}