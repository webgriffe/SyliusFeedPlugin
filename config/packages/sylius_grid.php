<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->extension('sylius_grid', [
        'grids' => [
            'setono_sylius_feed_admin_feed' => [
                'driver' => [
                    'name' => 'doctrine/orm',
                    'options' => [
                        'class' => '%setono_sylius_feed.model.feed.class%',
                    ],
                ],
                'fields' => [
                    'name' => [
                        'type' => 'string',
                        'label' => 'setono_sylius_feed.ui.name',
                    ],
                    'state' => [
                        'type' => 'twig',
                        'label' => 'setono_sylius_feed.ui.state',
                        'options' => [
                            'template' => '@SetonoSyliusFeedPlugin/Admin/Feed/Grid/Field/state.html.twig',
                        ],
                    ],
                ],
                'actions' => [
                    'main' => [
                        'create' => [
                            'type' => 'create',
                        ],
                    ],
                    'item' => [
                        'update' => [
                            'type' => 'update',
                        ],
                        'show' => [
                            'type' => 'show',
                        ],
                        'delete' => [
                            'type' => 'delete',
                        ],
                    ],
                ],
            ],
            'setono_sylius_feed_admin_violation' => [
                'driver' => [
                    'name' => 'doctrine/orm',
                    'options' => [
                        'class' => '%setono_sylius_feed.model.violation.class%',
                        'repository' => [
                            'method' => 'createQueryBuilderByFeed',
                            'arguments' => [
                                'feed' => '$id',
                            ],
                        ],
                    ],
                ],
                'limits' => [200, 100, 500, 1000],
                'fields' => [
                    'severity' => [
                        'type' => 'string',
                        'label' => 'setono_sylius_feed.ui.severity',
                    ],
                    'message' => [
                        'type' => 'string',
                        'label' => 'setono_sylius_feed.ui.message',
                    ],
                    'data' => [
                        'type' => 'twig',
                        'label' => 'setono_sylius_feed.ui.data',
                        'options' => [
                            'template' => '@SetonoSyliusFeedPlugin/Admin/Violation/Grid/Field/data.html.twig',
                        ],
                    ],
                ],
            ],
        ],
    ]);
};
