<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin;

use Setono\SyliusFeedPlugin\DependencyInjection\Compiler\RegisterFeedTypesPass;
use Setono\SyliusFeedPlugin\DependencyInjection\Compiler\RegisterFilesystemPass;
use Setono\SyliusFeedPlugin\DependencyInjection\Compiler\ValidateDataProvidersPass;
use Setono\SyliusFeedPlugin\Model\Feed;
use Setono\SyliusFeedPlugin\Model\Violation;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetonoSyliusFeedPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->setParameter('setono_sylius_feed.model.feed.class', Feed::class);
        $container->setParameter('setono_sylius_feed.model.violation.class', Violation::class);
        $container->addCompilerPass(new RegisterFeedTypesPass());
        $container->addCompilerPass(new RegisterFilesystemPass());
        $container->addCompilerPass(new ValidateDataProvidersPass());
    }

    #[\Override]
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    #[\Override]
    protected function getConfigFilesPath(): string
    {
        return sprintf(
            '%s/config/doctrine/%s',
            $this->getPath(),
            strtolower($this->getDoctrineMappingDirectory()),
        );
    }
}
