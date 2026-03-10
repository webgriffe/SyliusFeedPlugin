<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Message\Handler;

use Setono\SyliusFeedPlugin\Model\FeedInterface;
use Setono\SyliusFeedPlugin\Repository\FeedRepositoryInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

trait GetFeedTrait
{
    /** @var FeedRepositoryInterface<FeedInterface> */
    private FeedRepositoryInterface $feedRepository;

    private function getFeed(int $id): FeedInterface
    {
        $obj = $this->feedRepository->find($id);

        if (!$obj instanceof FeedInterface) {
            throw new UnrecoverableMessageHandlingException(sprintf('Feed with id %s does not exist', $id));
        }

        return $obj;
    }
}
