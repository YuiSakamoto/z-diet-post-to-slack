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
class OauthService
{

    const OAUTH_VERSION = '1.0';
    const OAUTH_SIGNATURE_METHOD = 'HMAC-SHA1';
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getRequestToken()
    {
        // https://oauth.withings.com/account/request_token?oauth_callback=https%3A%2F%2Fexample.com%2Fget_request_token&oauth_consumer_key=7ccd8909401e5494f8c0db8f5cbd57f69d4ece421fb09fe50b207c7d642&oauth_nonce=3f2db3e9543d42c4cae42def343a9ca6&oauth_signature=1seg73hdXHQhmqZUjDdHjN5280o%3D&oauth_signature_method=HMAC-SHA1&oauth_timestamp=1452597734&oauth_version=1.0
        $client = new Client([
            'base_uri' => $this->container['app.config']->get('request_url.request_token'),
            'timeout' => 2.0,
        ]);
        $oauth_nonce = $this->createOauthNonce();
        $oauth_timestamp = time();
        $oauth_signature_data = rawurlencode('GET') . '&'
            . rawurlencode($this->container['app.config']->get('request_url.request_token')). '&'
            . rawurlencode('oauth_callback=' . rawurlencode($this->container['app.config']->get('callback_url.authorize')) . '&')
            . rawurlencode('oauth_consumer_key=' . $this->container['app.config']->get('api_key') . '&')
            . rawurlencode('oauth_nonce=' . $oauth_nonce . '&')
            . rawurlencode('oauth_signature_method=' . self::OAUTH_SIGNATURE_METHOD . '&')
            . rawurlencode('oauth_timestamp=' . $oauth_timestamp . '&')
            . rawurlencode('oauth_version=' . self::OAUTH_VERSION);
        $oauth_signature = hash_hmac(
            'sha1',
            $oauth_signature_data,
            rawurlencode($this->container['app.config']->get('api_secret')) . '&',
            true
        );

        try {
            $response = $client->request('GET', $this->container['app.config']->get('request_url_request_token'), ['query' => [
                'oauth_callback'            => $this->container['app.config']->get('callback_url.authorize'),
                'oauth_consumer_key'        => $this->container['app.config']->get('api_key'),
                'oauth_nonce'               => $oauth_nonce,
                'oauth_signature'           => base64_encode($oauth_signature),
                'oauth_signature_method'    => self::OAUTH_SIGNATURE_METHOD,
                'oauth_timestamp'           => $oauth_timestamp,
                'oauth_version'             => self::OAUTH_VERSION,
            ]]);
        } catch (\Exception $e) {
            var_dump($e) ;
            exit;
        }
        $res_string = $response->getBody()->getContents();
        $tokens = explode('&', $res_string);
        $tokens['oauth_token'] = str_replace('oauth_token=', '', $tokens[0]);
        $tokens['oauth_token_secret'] = str_replace('oauth_token_secret=', '', $tokens[1]);

        return $tokens;
    }

    public function redirectAuthorize(array $tokens)
    {
        $oauth_nonce = $this->createOauthNonce();
        $oauth_timestamp = time();
        $oauth_signature_data = rawurlencode('GET') . '&'
            . rawurlencode($this->container['app.config']->get('request_url.authorize')). '&'
            . rawurlencode('oauth_consumer_key=' . $this->container['app.config']->get('api_key') . '&')
            . rawurlencode('oauth_nonce=' . $oauth_nonce . '&')
            . rawurlencode('oauth_signature_method=' . self::OAUTH_SIGNATURE_METHOD . '&')
            . rawurlencode('oauth_timestamp=' . $oauth_timestamp . '&')
            . rawurlencode('oauth_token=' . $tokens['oauth_token'] . '&')
            . rawurlencode('oauth_version=' . self::OAUTH_VERSION);
        $oauth_signature = hash_hmac(
            'sha1',
            $oauth_signature_data,
            rawurlencode($this->container['app.config']->get('api_secret')) . '&' . rawurlencode($tokens['oauth_token_secret']),
            true
        );

        $url = $this->container['app.config']->get('request_url.authorize');
        $url .= '?' . 'oauth_consumer_key=' . rawurlencode($this->container['app.config']->get('api_key')) . '&'
            . 'oauth_nonce=' .rawurlencode( $oauth_nonce) . '&'
            . 'oauth_signature=' . rawurlencode(base64_encode($oauth_signature)) . '&'
            . 'oauth_signature_method=' . rawurlencode(self::OAUTH_SIGNATURE_METHOD) . '&'
            . 'oauth_timestamp=' . rawurlencode($oauth_timestamp) . '&'
            . 'oauth_token=' . rawurlencode($tokens['oauth_token']) . '&'
            . 'oauth_version=' . rawurlencode(self::OAUTH_VERSION);
        header("Location: {$url}");
        exit;
    }

    private function createOauthNonce($length = 32)
    {
        return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
    }
}