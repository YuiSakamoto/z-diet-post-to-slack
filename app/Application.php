<?php

namespace ZDiet;

use Dietcube\Application as DCApplication;
use Dietcube\Controller as DCController;
use Pimple\Container;
use ZDiet\Service\OauthService;

/**
 * Created by IntelliJ IDEA.
 * User: yui_tang
 * Date: 1/12/16
 * Time: 19:02
 */
class Application extends DCApplication
{
    public function init(Container $container)
    {
        parent::init($container); // TODO: Change the autogenerated stub
    }

    public function config(Container $container)
    {
        $container['service.Oauth'] = function () use ($container) {
            return new OauthService(
                $container
            );
        };
    }
}