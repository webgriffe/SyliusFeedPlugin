<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Setono\SyliusFeedPlugin\Form\Type\FeedType;
use Setono\SyliusFeedPlugin\Form\Type\FeedTypeChoiceType;

return static function (ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->parameters()
        ->set('setono_sylius_feed.form.type.feed.validation_groups', [
            'setono_sylius_feed',
        ])
    ;
    $services = $containerConfigurator->services();

    $services->set('setono_sylius_feed.form.type.feed', FeedType::class)
        ->args([
            param('setono_sylius_feed.model.feed.class'),
            param('setono_sylius_feed.form.type.feed.validation_groups'),
        ])
        ->tag('form.type')
    ;

    $services->set('setono_sylius_feed.form.type.feed_type_choice', FeedTypeChoiceType::class)
        ->args([
            service('setono_sylius_feed.registry.feed_type'),
        ])
        ->tag('form.type')
    ;
};
