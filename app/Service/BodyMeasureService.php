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
class BodyMeasureService extends AbstractApiService
{

    public function getData($users)
    {
        $data = [];
        foreach ($users as $user) {
            $data[$user->getName()] = $this->getUserData($user);
        }
        return $data;
    }

    /**
     * @param \ZDiet\Entity\User $user
     */
    protected function getUserData($user)
    {
        $oauth_nonce = $this->createOauthNonce();
        $oauth_timestamp = time();
        $oauth_signature_data = rawurlencode('GET') . '&'
            . rawurlencode($this->container['app.config']->get('request_url.measure')) . '&' . rawurlencode('action=getmeas&')
            . rawurlencode('meastype=1&')
            . rawurlencode('oauth_consumer_key=' . $this->api_key . '&')
            . rawurlencode('oauth_nonce=' . $oauth_nonce . '&')
            . rawurlencode('oauth_signature_method=' . self::OAUTH_SIGNATURE_METHOD . '&')
            . rawurlencode('oauth_timestamp=' . $oauth_timestamp . '&')
            . rawurlencode('oauth_token=' . $user->getOauthToken() . '&')
            . rawurlencode('oauth_version=' . self::OAUTH_VERSION . '&')
            . rawurlencode('startdate=1452265200&')
            . rawurlencode('userid=' . $user->getUserId());
        $oauth_signature = hash_hmac(
            'sha1',
            $oauth_signature_data,
            rawurlencode($this->api_key_secret) . '&' . rawurlencode($user->getOauthTokenSecret()),
            true
        );

        try {
            $query = ['query' => [
                'action' => 'getmeas',
                'oauth_consumer_key'        => $this->api_key,
                'oauth_nonce'               => $oauth_nonce,
                'oauth_signature'           => base64_encode($oauth_signature),
                'oauth_signature_method'    => self::OAUTH_SIGNATURE_METHOD,
                'oauth_timestamp'           => $oauth_timestamp,
                'oauth_token'               => $user->getOauthToken(),
                'oauth_version'             => self::OAUTH_VERSION,
                'userid'                    => $user->getUserId(),
                'startdate'                 => 1452265200,
                'meastype'                  => 1,
            ]];
            $response = $this->client->request('GET', $this->container['app.config']->get('request_url.measure'), $query);
        } catch (\Exception $e) {
            $response = $this->client->request('GET', $this->container['app.config']->get('request_url.measure'), $query);
        }
        $res_string = $response->getBody()->getContents();
        $res_array = json_decode($res_string, true);
        return [
            'latest' => current($res_array['body']['measuregrps']),
            'reference' => end($res_array['body']['measuregrps']),
        ];
    }

}