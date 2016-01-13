<?php
/**
 * Created by IntelliJ IDEA.
 * User: yui_tang
 * Date: 1/12/16
 * Time: 19:03
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Dietcube\Dispatcher;

Dispatcher::invoke(
    '\\ZDiet\\Application',
    dirname(__DIR__) . '/app',
    Dispatcher::getEnv()
);