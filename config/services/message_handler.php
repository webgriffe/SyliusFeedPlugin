<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Message\Handler\FinishGenerationHandler;
use Setono\SyliusFeedPlugin\Message\Handler\GenerateBatchHandler;
use Setono\SyliusFeedPlugin\Message\Handler\GenerateFeedHandler;
use Setono\SyliusFeedPlugin\Message\Handler\ProcessFeedHandler;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(FinishGenerationHandler::class)
        ->args([
            service('setono_sylius_feed.repository.feed'),
            service('setono_sylius_feed.manager.feed'),
            service('setono_sylius_feed.storage.feed_tmp'),
            service('workflow.registry'),
            service('twig'),
            service('setono_sylius_feed.registry.feed_type'),
            service('setono_sylius_feed.generator.temporary_feed_path'),
            service('logger'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set(GenerateBatchHandler::class)
        ->args([
            service('setono_sylius_feed.repository.feed'),
            service('sylius.repository.channel'),
            service('sylius.repository.locale'),
            service('setono_sylius_feed.manager.feed'),
            service('setono_sylius_feed.registry.feed_type'),
            service('twig'),
            service('setono_sylius_feed.storage.feed_tmp'),
            service('setono_sylius_feed.generator.temporary_feed_path'),
            service('event_dispatcher'),
            service('workflow.registry'),
            service('validator'),
            service('setono_sylius_feed.factory.violation'),
            service('serializer'),
            service('router'),
            service('logger'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set(GenerateFeedHandler::class)
        ->args([
            service('setono_sylius_feed.repository.feed'),
            service('sylius.repository.channel'),
            service('sylius.repository.locale'),
            service('setono_sylius_feed.registry.feed_type'),
            service('setono_sylius_feed.command_bus'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set(ProcessFeedHandler::class)
        ->args([
            service('setono_sylius_feed.repository.feed'),
            service('setono_sylius_feed.manager.feed'),
            service('setono_sylius_feed.registry.feed_type'),
            service('setono_sylius_feed.command_bus'),
            service('workflow.registry'),
            service('setono_sylius_feed.validator.template'),
        ])
        ->tag('messenger.message_handler')
    ;
};
