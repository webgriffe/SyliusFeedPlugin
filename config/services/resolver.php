<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Resolver\FeedExtensionResolver;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.resolver.feed_extension', FeedExtensionResolver::class)
        ->args([
            service('setono_sylius_feed.registry.feed_type'),
            service('twig'),
        ])
    ;
};
