<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('setono_sylius_feed_shop_feed_show', '/feed/{code}')
        ->controller('setono_sylius_feed.controller.action.shop.show_feed')
        ->methods(['GET'])
    ;
};
