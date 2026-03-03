<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\DTO;

final readonly class SeverityCount
{
    public function __construct(private string $severity, private int $count)
    {
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
