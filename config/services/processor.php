<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Processor\FeedProcessor;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.processor.feed', FeedProcessor::class)
        ->args([
            service('setono_sylius_feed.repository.feed'),
            service('setono_sylius_feed.command_bus'),
        ])
    ;
};
