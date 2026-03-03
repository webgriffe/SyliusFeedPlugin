<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Event;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusFeedPlugin\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

final readonly class QueryBuilderEvent
{
    public function __construct(
        private DataProviderInterface $dataProvider,
        private QueryBuilder $queryBuilder,
        private ChannelInterface $channel,
        private LocaleInterface $locale,
    ) {
    }

    public function getDataProvider(): DataProviderInterface
    {
        return $this->dataProvider;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getLocale(): LocaleInterface
    {
        return $this->locale;
    }
}
