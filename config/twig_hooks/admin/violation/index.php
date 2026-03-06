<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.violation.index.content.header' => [
                'breadcrumbs' => [
                    'template' => '@SetonoSyliusFeedPlugin/admin/violation/index/content/header/breadcrumbs.html.twig',
                    'priority' => 100,
                ],
            ],
        ],
    ]);
};
