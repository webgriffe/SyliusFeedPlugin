<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Generator\FeedPathGenerator;
use Setono\SyliusFeedPlugin\Generator\TemporaryFeedPathGenerator;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.generator.feed_path', FeedPathGenerator::class)
        ->args([
            service('setono_sylius_feed.resolver.feed_extension'),
        ])
    ;

    $services->set('setono_sylius_feed.generator.temporary_feed_path', TemporaryFeedPathGenerator::class)
    ;
};
