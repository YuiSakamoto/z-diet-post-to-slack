<?php
/**
 * Created by IntelliJ IDEA.
 * User: yui_tang
 * Date: 1/12/16
 * Time: 19:47
 */

namespace ZDiet;

use Dietcube\RouteInterface;
use Pimple\Container;

class Route implements RouteInterface
{
    public function definition(Container $container)
    {
        // TODO: Implement definition() method.
        return [
            ['GET', '/', 'Index::index'],
            ['GET', '/authorize', 'Index::authorize'],
        ];
    }
}