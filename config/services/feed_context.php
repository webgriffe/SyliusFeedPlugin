<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\FeedContext\Google\Shopping\FeedContext;
use Setono\SyliusFeedPlugin\FeedContext\Google\Shopping\ProductItemContext;

return static function (ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->parameters()->set(
        'setono_sylius_feed.product_item.exclude_root_taxon',
        false,
    );

    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.feed_context.google.shopping', FeedContext::class);

    $services->set('setono_sylius_feed.feed_context.google.shopping.product_item', ProductItemContext::class)
        ->args([
            service('router'),
            service('liip_imagine.cache.manager'),
            service('sylius.checker.inventory.availability'),
            param('setono_sylius_feed.product_item.exclude_root_taxon'),
        ])
    ;
};
