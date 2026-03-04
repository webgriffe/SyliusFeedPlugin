<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->extension('flysystem', [
        'storages' => [
            'setono_sylius_feed.storage.local.feed_tmp' => [
                'adapter' => 'local',
                'options' => [
                    'directory' => '%kernel.project_dir%/var/storage/setono_sylius_feed/feed_tmp',
                ],
            ],
            'setono_sylius_feed.storage.local.feed' => [
                'adapter' => 'local',
                'options' => [
                    'directory' => '%kernel.project_dir%/var/storage/setono_sylius_feed/feed',
                ],
            ],
        ],
    ]);
};
