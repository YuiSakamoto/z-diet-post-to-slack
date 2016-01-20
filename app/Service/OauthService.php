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
class OauthService extends AbstractApiService
{

    public function getRequestToken()
    {
        $oauth_nonce = $this->createOauthNonce();
        $oauth_timestamp = time();
        $oauth_signature_data = rawurlencode('GET') . '&'
            . rawurlencode($this->container['app.config']->get('request_url.request_token')). '&'
            . rawurlencode('oauth_callback=' . rawurlencode($this->container['app.config']->get('callback_url.authorize')) . '&')
            . rawurlencode('oauth_consumer_key=' . $this->api_key . '&')
            . rawurlencode('oauth_nonce=' . $oauth_nonce . '&')
            . rawurlencode('oauth_signature_method=' . self::OAUTH_SIGNATURE_METHOD . '&')
            . rawurlencode('oauth_timestamp=' . $oauth_timestamp . '&')
            . rawurlencode('oauth_version=' . self::OAUTH_VERSION);
        $oauth_signature = hash_hmac(
            'sha1',
            $oauth_signature_data,
            rawurlencode($this->api_key_secret) . '&',
            true
        );

        try {
            $query = ['query' => [
                'oauth_callback'            => $this->container['app.config']->get('callback_url.authorize'),
                'oauth_consumer_key'        => $this->api_key,
                'oauth_nonce'               => $oauth_nonce,
                'oauth_signature'           => base64_encode($oauth_signature),
                'oauth_signature_method'    => self::OAUTH_SIGNATURE_METHOD,
                'oauth_timestamp'           => $oauth_timestamp,
                'oauth_version'             => self::OAUTH_VERSION,
            ]];
            $response = $this->client->request('GET', $this->container['app.config']->get('request_url_request_token'), $query);
        } catch (\Exception $e) {
            $response = $this->client->request('GET', $this->container['app.config']->get('request_url_request_token'), $query);
        }
        $res_string = $response->getBody()->getContents();
        $tokens = explode('&', $res_string);
        $tokens['oauth_token'] = str_replace('oauth_token=', '', $tokens[0]);
        $tokens['oauth_token_secret'] = str_replace('oauth_token_secret=', '', $tokens[1]);

        file_put_contents($this->oauth_token_secret_path, $tokens['oauth_token_secret']);
        return $tokens;
    }

    public function redirectAuthorize(array $tokens)
    {
        $oauth_nonce = $this->createOauthNonce();
        $oauth_timestamp = time();
        $oauth_signature_data = rawurlencode('GET') . '&'
            . rawurlencode($this->container['app.config']->get('request_url.authorize')). '&'
            . rawurlencode('oauth_consumer_key=' . $this->api_key . '&')
            . rawurlencode('oauth_nonce=' . $oauth_nonce . '&')
            . rawurlencode('oauth_signature_method=' . self::OAUTH_SIGNATURE_METHOD . '&')
            . rawurlencode('oauth_timestamp=' . $oauth_timestamp . '&')
            . rawurlencode('oauth_token=' . $tokens['oauth_token'] . '&')
            . rawurlencode('oauth_version=' . self::OAUTH_VERSION);
        $oauth_signature = hash_hmac(
            'sha1',
            $oauth_signature_data,
            rawurlencode($this->api_key_secret) . '&' . rawurlencode($tokens['oauth_token_secret']),
            true
        );

        $url = $this->container['app.config']->get('request_url.authorize');
        $url .= '?' . 'oauth_consumer_key=' . rawurlencode($this->api_key) . '&'
            . 'oauth_nonce=' .rawurlencode( $oauth_nonce) . '&'
            . 'oauth_signature=' . rawurlencode(base64_encode($oauth_signature)) . '&'
            . 'oauth_signature_method=' . rawurlencode(self::OAUTH_SIGNATURE_METHOD) . '&'
            . 'oauth_timestamp=' . rawurlencode($oauth_timestamp) . '&'
            . 'oauth_token=' . rawurlencode($tokens['oauth_token']) . '&'
            . 'oauth_version=' . rawurlencode(self::OAUTH_VERSION);
        header("Location: {$url}");
        exit;
    }

    public function generateAccessToken($oauth_token)
    {
        $this->client = new Client([
            'base_uri' => $this->container['app.config']->get('request_url.access_token'),
            'timeout' => 2.0,
        ]);
        $oauth_nonce = $this->createOauthNonce();
        $oauth_timestamp = time();
        $oauth_signature_data = rawurlencode('GET') . '&'
            . rawurlencode($this->container['app.config']->get('request_url.access_token')). '&'
            . rawurlencode('oauth_consumer_key=' . $this->api_key . '&')
            . rawurlencode('oauth_nonce=' . $oauth_nonce . '&')
            . rawurlencode('oauth_signature_method=' . self::OAUTH_SIGNATURE_METHOD . '&')
            . rawurlencode('oauth_timestamp=' . $oauth_timestamp . '&')
            . rawurlencode('oauth_token=' . $oauth_token . '&')
            . rawurlencode('oauth_version=' . self::OAUTH_VERSION);
        $oauth_signature = hash_hmac(
            'sha1',
            $oauth_signature_data,
            rawurlencode($this->api_key_secret) . '&' . rawurlencode(file_get_contents($this->oauth_token_secret_path)),
            true
        );

        try {
            $query = ['query' => [
                'oauth_consumer_key'        => $this->api_key,
                'oauth_nonce'               => $oauth_nonce,
                'oauth_signature'           => base64_encode($oauth_signature),
                'oauth_signature_method'    => self::OAUTH_SIGNATURE_METHOD,
                'oauth_timestamp'           => $oauth_timestamp,
                'oauth_token'               => $oauth_token,
                'oauth_version'             => self::OAUTH_VERSION,
            ]];
            $response = $this->client->request('GET', $this->container['app.config']->get('request_url.access_token'), $query);
        } catch (\Exception $e) {
            $response = $this->client->request('GET', $this->container['app.config']->get('request_url.access_token'), $query);
        }

        $res_string = $response->getBody()->getContents();
        $responses = explode('&', $res_string);
        return array_map(function($v) {
            return  preg_replace('/^[^=]*=/', '', $v);
        }, $responses);
    }

    public function saveTokens(array $tokens)
    {
        if (!file_exists($this->user_tokens_dir_path)) {
            mkdir($this->user_tokens_dir_path);
        }

        file_put_contents($this->user_tokens_dir_path . '/' . $tokens[2], $tokens[2] . "\t" . $tokens[0] . "\t" . $tokens[1]);
    }

    public function saveName($userid, $name)
    {
        $raw_data = file_get_contents($this->user_tokens_dir_path . '/' . $userid);
        file_put_contents($this->user_tokens_dir_path . '/' . $userid, $raw_data . "\t" . $name);
    }

}