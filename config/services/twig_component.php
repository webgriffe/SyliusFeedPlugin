<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Twig\Component\SeverityCountComponent;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.twig.component.violation.severity_count', SeverityCountComponent::class)
        ->args([
            service('setono_sylius_feed.repository.violation'),
        ])
        ->tag('sylius.live_component.admin', [
            'key' => 'setono_sylius_feed:violation:severity_count',
        ])
    ;
};
