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
class PostService
{

    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function post($response)
    {
        $text = $this->createText($response);
        $post_url = $this->container['app.config']->get('slack_webhook_url');
        $ch = curl_init($post_url);
        $payload = [
            'text'   => $text,
        ];
        $jsonDataEncoded = json_encode($payload);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result = curl_exec($ch);
        var_dump($result);
    }

    protected function createText($respose)
    {
        $text = "【今日の結果】\n";
        $caluced_array = [];
        foreach($respose as $name => $user_data) {
            // パーセント計算
            $parcent = 100 * round($user_data['latest']['measures'][0]['value'] / $user_data['reference']['measures'][0]['value'], 4);
            $caluced_array[$name] = $parcent;
        }
        asort($caluced_array);

        $count = 1;
        foreach($caluced_array as $name => $v) {
            $text .= $count . '位:' . $name . ' => ' . $v . "%\n";
            $count++;
        }
        return $text;
    }
}