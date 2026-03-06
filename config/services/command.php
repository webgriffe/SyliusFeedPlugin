<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Command\ProcessFeedsCommand;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.command.process_feeds', ProcessFeedsCommand::class)
        ->args([
            service('setono_sylius_feed.processor.feed'),
        ])
        ->tag('console.command')
    ;
};
