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
        $text = "5th season has begun!!\nToday's results(Compared to 1st Jan.)\n";
        $caluced_array = [];
        foreach($respose as $name => $user_data) {
            if (!$user_data['latest']['measures'][0]['value'] || !$user_data['reference']['measures'][0]['value']) {
                continue;
            }
            // パーセント計算
            $unit = $user_data['latest']['measures'][0]['unit'] + $user_data['reference']['measures'][0]['unit'];
            $parcent = round($user_data['latest']['measures'][0]['value'] / $user_data['reference']['measures'][0]['value'] * (10 ** (-$unit)) / 10000, 4);
            $caluced_array[$name] = $parcent;
        }
        asort($caluced_array);

        $count = 1;
        foreach($caluced_array as $name => $v) {
            if ($count === 1) {
                $text .= $count . 'st';
            } elseif ($count === 2) {
                $text .= $count . 'nd';
            } elseif ($count === 3) {
                $text .= $count . 'rd';
            } else {
                $text .= $count . 'th';
            }
            $text .= ':' . $name . ' => ' . $v . "%\n";
            $count++;
        }
        return $text;
    }
}
