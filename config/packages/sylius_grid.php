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
                            'template' => '@SetonoSyliusFeedPlugin/admin/feed/grid/field/state.html.twig',
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
                        'show' => [
                            'type' => 'show',
                        ],
                        'update' => [
                            'type' => 'update',
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
                'limits' => [100, 200, 500, 1000],
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
                            'template' => '@SetonoSyliusFeedPlugin/admin/violation/grid/field/data.html.twig',
                        ],
                    ],
                ],
                'filters' => [
                    'severity' => [
                        'type' => 'select',
                        'label' => 'setono_sylius_feed.ui.severity',
                        'form_options' => [
                            'choices' => [
                                'setono_sylius_feed.ui.severities.error' => 'error',
                                'setono_sylius_feed.ui.severities.warning' => 'warning',
                                'setono_sylius_feed.ui.severities.notice' => 'notice',
                            ],
                            'multiple' => false,
                        ],
                    ],
                ],
            ],
        ],
    ]);
};
