<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Factory\ViolationFactory;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.custom_factory.violation', ViolationFactory::class)
        ->decorate('setono_sylius_feed.factory.violation')
        ->args([
            service('.inner'),
        ])
    ;
};
