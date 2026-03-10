<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Repository;

use Setono\SyliusFeedPlugin\Model\FeedInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of FeedInterface
 * @extends RepositoryInterface<T>
 */
interface FeedRepositoryInterface extends RepositoryInterface
{
    public function findOneByCode(string $code): ?FeedInterface;

    /**
     * Returns all enabled feeds
     *
     * @return array<array-key, FeedInterface>
     */
    public function findEnabled(): array;

    /**
     * Increments the finished batches count by 1
     */
    public function incrementFinishedBatches(FeedInterface $feed): void;

    /**
     * Returns true if all batches for the given feed has been generated
     */
    public function batchesGenerated(FeedInterface $feed): bool;
}
