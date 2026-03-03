<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Event;

use Setono\SyliusFeedPlugin\Model\FeedInterface;

final readonly class BatchGeneratedEvent
{
    public function __construct(private FeedInterface $feed)
    {
    }

    public function getFeed(): FeedInterface
    {
        return $this->feed;
    }
}
