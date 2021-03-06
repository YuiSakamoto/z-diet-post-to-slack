<?php

namespace ZDiet\Controller;

use Dietcube\Controller;

/**
 * Created by IntelliJ IDEA.
 * User: yui_tang
 * Date: 1/12/16
 * Time: 19:50
 */
class IndexController extends Controller
{
    public function index()
    {
        /* @var \ZDiet\Service\OauthService */
        $oauth_service = $this->container['service.Oauth'];
        $tokens = $oauth_service->getRequestToken();
        $oauth_service->redirectAuthorize($tokens);
    }

    public function authorize()
    {
        $oauth_token = $this->query('oauth_token');
        $oauth_service = $this->container['service.Oauth'];

        $responses = $oauth_service->generateAccessToken($oauth_token);
        $oauth_service->saveTokens($responses);

        return $this->render('index/input_nickname', [
            'userid' => $responses[2],
        ]);
    }

    public function finish()
    {
        $name = $this->body('name');
        $userid = $this->body('userid');
        $oauth_service = $this->container['service.Oauth'];

        $oauth_service->saveName($userid, $name);
        return $this->render('index/finish');
    }
}