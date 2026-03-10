<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Event\QueryBuilderEvent;
use Setono\SyliusFeedPlugin\EventListener\DeleteGeneratedFilesSubscriber;
use Setono\SyliusFeedPlugin\EventListener\Filter\ChannelFilterListener;
use Setono\SyliusFeedPlugin\EventListener\Filter\EnabledFilterListener;
use Setono\SyliusFeedPlugin\EventListener\IncrementFinishedBatchesSubscriber;
use Setono\SyliusFeedPlugin\EventListener\MoveGeneratedFeedSubscriber;
use Setono\SyliusFeedPlugin\EventListener\SendFinishGenerationCommandSubscriber;
use Setono\SyliusFeedPlugin\EventListener\StartProcessingSubscriber;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.event_listener.delete_generated_files', DeleteGeneratedFilesSubscriber::class)
        ->args([
            service('setono_sylius_feed.storage.feed_tmp'),
        ])
        ->tag('kernel.event_subscriber')
    ;

    $services->set('setono_sylius_feed.event_listener.increment_finished_batches', IncrementFinishedBatchesSubscriber::class)
        ->args([
            service('setono_sylius_feed.repository.feed'),
        ])
        ->tag('kernel.event_subscriber')
    ;

    $services->set('setono_sylius_feed.event_listener.move_generated_feed', MoveGeneratedFeedSubscriber::class)
        ->args([
            service('setono_sylius_feed.storage.feed_tmp'),
            service('setono_sylius_feed.storage.feed'),
            service('setono_sylius_feed.generator.temporary_feed_path'),
            service('setono_sylius_feed.generator.feed_path'),
        ])
        ->tag('kernel.event_subscriber')
    ;

    $services->set('setono_sylius_feed.event_listener.send_finish_generation_command', SendFinishGenerationCommandSubscriber::class)
        ->args([
            service('setono_sylius_feed.repository.feed'),
            service('setono_sylius_feed.command_bus'),
        ])
        ->tag('kernel.event_subscriber')
    ;

    $services->set('setono_sylius_feed.event_listener.start_processing', StartProcessingSubscriber::class)
        ->args([
            service('setono_sylius_feed.registry.feed_type'),
        ])
        ->tag('kernel.event_subscriber')
    ;

    $services->set('setono_sylius_feed.event_listener.filter.channel', ChannelFilterListener::class)
        ->args([
            param('sylius.model.product.class'),
        ])
        ->tag('kernel.event_listener', [
            'event' => QueryBuilderEvent::class,
            'method' => 'filter',
        ])
    ;

    $services->set('setono_sylius_feed.event_listener.filter.enabled', EnabledFilterListener::class)
        ->args([
            param('sylius.model.product.class'),
        ])
        ->tag('kernel.event_listener', [
            'event' => QueryBuilderEvent::class,
            'method' => 'filter',
        ])
    ;
};
