<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Registry\FeedTypeRegistry;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.registry.feed_type', FeedTypeRegistry::class)
    ;
};
