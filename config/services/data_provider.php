<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\DataProvider\DataProvider;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.data_provider.parent', DataProvider::class)
        ->abstract()
        ->args([
            service('setono_doctrine_orm_batcher.factory.batcher'),
            service('setono_doctrine_orm_batcher.query.rebuilder'),
            service('event_dispatcher'),
            service('doctrine'),
        ])
    ;

    $services->set('setono_sylius_feed.data_provider.product', DataProvider::class)
        ->parent('setono_sylius_feed.data_provider.parent')
        ->args([
            param('sylius.model.product.class'),
        ])
        ->tag('setono_sylius_feed.data_provider', [
            'code' => 'product',
        ])
    ;

    $services->set('setono_sylius_feed.data_provider.product_variant', DataProvider::class)
        ->parent('setono_sylius_feed.data_provider.parent')
        ->args([
            param('sylius.model.product_variant.class'),
        ])
        ->tag('setono_sylius_feed.data_provider', [
            'code' => 'product_variant',
        ])
    ;
};
