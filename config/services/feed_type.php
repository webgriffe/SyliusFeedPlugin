<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\FeedType\FeedType;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.feed_type.google_shopping', FeedType::class)
        ->args([
            'google_shopping',
            '@SetonoSyliusFeedPlugin/Feed/Google/Shopping/feed.txt.twig',
            service('setono_sylius_feed.data_provider.product'),
            service('setono_sylius_feed.feed_context.google.shopping'),
            service('setono_sylius_feed.feed_context.google.shopping.product_item'),
        ])
        ->tag('setono_sylius_feed.feed_type')
    ;
};
