<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Generator\FeedPathGenerator;
use Setono\SyliusFeedPlugin\Generator\TemporaryFeedPathGenerator;
use Setono\SyliusFeedPlugin\Menu\AdminMenuListener;
use Setono\SyliusFeedPlugin\Menu\FeedShowMenuBuilder;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(AdminMenuListener::class)
        ->tag('kernel.event_listener', [
            'event' => 'sylius.menu.admin.main',
            'method' => 'addAdminMenuItems',
        ])
    ;

    $services->set('setono_sylius_feed.admin.menu_builder.feed.show', FeedShowMenuBuilder::class)
        ->args([
            service('knp_menu.factory'),
            service('event_dispatcher'),
            service('workflow.registry'),
        ])
        ->tag('knp_menu.menu_builder', [
            'method' => 'createMenu',
            'alias' => 'setono_sylius_feed.admin.feed.show',
        ])
    ;
};
