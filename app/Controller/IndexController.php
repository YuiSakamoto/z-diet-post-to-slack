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
        var_dump($this->query('userid'));exit;
    }
}