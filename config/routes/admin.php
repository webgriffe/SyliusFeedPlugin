<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import(<<<YAML
alias: setono_sylius_feed.feed
section: admin
templates: "@SyliusAdmin/shared/crud"
redirect: show
grid: setono_sylius_feed_admin_feed
permission: true
vars:
    all:
        subheader: setono_sylius_feed.ui.manage_feeds
    index:
        icon: 'file image outline'
YAML
, 'sylius.resource');

    $routes->add('setono_sylius_feed_admin_feed_process', '/feeds/{id}/process')
        ->controller('setono_sylius_feed.controller.action.admin.process_feed')
        ->methods(['GET'])
    ;

    $routes->add('setono_sylius_feed_admin_feed_violations_index', '/feeds/{id}/violations')
        ->controller(['setono_sylius_feed.controller.violation', 'indexAction'])
        ->methods(['GET'])
        ->defaults([
            '_sylius' => [
                'vars' => [
                    'route' => [
                        'parameters' => [
                            'id' => '$id',
                        ],
                    ],
                ],
                'template' => '@SyliusAdmin/shared/crud/index.html.twig',
                'grid' => 'setono_sylius_feed_admin_violation',
                'section' => 'admin',
                'permission' => true,
            ],
        ])
    ;
};
