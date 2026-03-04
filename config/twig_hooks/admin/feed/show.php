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
                'general' => [
                    'template' => '@SetonoSyliusFeedPlugin/admin/feed/show/content/sections/general.html.twig',
                    'priority' => 100,
                ],
                'violations' => [
                    'template' => '@SetonoSyliusFeedPlugin/admin/feed/show/content/sections/violations.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_admin.feed.show.content.sections.general' => [
                'name' => [
                    'template' => '@SetonoSyliusFeedPlugin/admin/feed/show/content/sections/general/name.html.twig',
                    'priority' => 100,
                ],
            ],
        ],
    ]);
};
