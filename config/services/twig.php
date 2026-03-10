<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Twig\Extension;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.twig.extension', Extension::class)
        ->args([
            service('request_stack'),
            service('router'),
            service('setono_sylius_feed.repository.feed'),
        ])
        ->tag('twig.extension')
    ;
};
