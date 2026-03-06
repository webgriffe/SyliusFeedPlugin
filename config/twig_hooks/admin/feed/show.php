<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.feed.show.content' => [
                'sections' => [
                    'template' => '@SetonoSyliusFeedPlugin/admin/feed/show/content/sections.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_admin.feed.show.content.header' => [
                'breadcrumbs' => [
                    'template' => '@SyliusAdmin/shared/crud/show/content/header/breadcrumbs.html.twig',
                    'configuration' => [
                        'rendered_field' => 'name',
                    ],
                    'priority' => 0,
                ],
            ],

            'sylius_admin.feed.show.content.header.title_block' => [
                'title' => [
                    'template' => '@SetonoSyliusFeedPlugin/admin/feed/show/content/header/title_block/title.html.twig',
                    'priority' => 100,
                ],
                'actions' => [
                    'template' => '@SetonoSyliusFeedPlugin/admin/feed/show/content/header/title_block/actions.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_admin.feed.show.content.header.title_block.actions' => [
                'process' => [
                    'template' => '@SetonoSyliusFeedPlugin/admin/feed/show/content/header/title_block/actions/process.html.twig',
                    'priority' => 200,
                ],
                'edit' => [
                    'template' => '@SetonoSyliusFeedPlugin/admin/feed/show/content/header/title_block/actions/edit.html.twig',
                    'priority' => 100,
                ],
                'delete' => [
                    'template' => '@SetonoSyliusFeedPlugin/admin/feed/show/content/header/title_block/actions/delete.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_admin.feed.show.content.sections' => [
                'channel_urls' => [
                    'template' => '@SetonoSyliusFeedPlugin/admin/feed/show/content/sections/channel_urls.html.twig',
                    'priority' => 200,
                ],
                'violations' => [
                    'template' => '@SetonoSyliusFeedPlugin/admin/feed/show/content/sections/violations.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_admin.feed.show.content.sections.violations' => [
                'severity_count' => [
                    'component' => 'setono_sylius_feed:violation:severity_count',
                    'props' => [
                        'feed' => '@=_context.feed.getId()',
                        'template' => '@SetonoSyliusFeedPlugin/admin/feed/show/component/severity_count.html.twig',
                    ],
                ],
            ],
        ],
    ]);
};
