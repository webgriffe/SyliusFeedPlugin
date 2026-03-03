<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Message\Command;

final readonly class FinishGeneration implements CommandInterface
{
    public function __construct(private int $feedId)
    {
    }

    public function getFeedId(): int
    {
        return $this->feedId;
    }
}
