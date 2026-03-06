<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Controller\Action\Admin\ProcessFeedAction;
use Setono\SyliusFeedPlugin\Controller\Action\Admin\ProcessFeedActionInterface;
use Setono\SyliusFeedPlugin\Controller\Action\Admin\SeverityCountAction;
use Setono\SyliusFeedPlugin\Controller\Action\Admin\SeverityCountActionInterface;
use Setono\SyliusFeedPlugin\Controller\Action\Shop\ShowFeedAction;
use Setono\SyliusFeedPlugin\Controller\Action\Shop\ShowFeedActionInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.controller.action.admin.process_feed', ProcessFeedAction::class)
        ->args([
            service('setono_sylius_feed.command_bus'),
            service('translator'),
        ])
        ->call('setContainer', [service('service_container')])
        ->tag('controller.service_arguments')
        ->tag('container.service_subscriber')
    ;

    $services->alias(ProcessFeedActionInterface::class, 'setono_sylius_feed.controller.action.admin.process_feed');

    $services->set('setono_sylius_feed.controller.action.admin.severity_count', SeverityCountAction::class)
        ->args([
            service('setono_sylius_feed.repository.violation'),
        ])
        ->call('setContainer', [service('service_container')])
        ->tag('controller.service_arguments')
        ->tag('container.service_subscriber')
    ;

    $services->alias(SeverityCountActionInterface::class, 'setono_sylius_feed.controller.action.admin.severity_count');

    $services->set('setono_sylius_feed.controller.action.shop.show_feed', ShowFeedAction::class)
        ->args([
            service('setono_sylius_feed.repository.feed'),
            service('sylius.context.channel'),
            service('sylius.context.locale'),
            service('setono_sylius_feed.generator.feed_path'),
            service('setono_sylius_feed.storage.feed'),
            service('mime_types'),
        ])
        ->call('setContainer', [service('service_container')])
        ->tag('controller.service_arguments')
        ->tag('container.service_subscriber')
    ;

    $services->alias(ShowFeedActionInterface::class, 'setono_sylius_feed.controller.action.shop.show_feed');
};
