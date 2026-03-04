<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'buses' =>[
                'setono_sylius_feed.command_bus' => null,
            ]
        ],
    ]);
};
