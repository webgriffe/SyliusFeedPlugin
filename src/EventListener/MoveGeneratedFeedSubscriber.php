<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\EventListener;

use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToDeleteFile;
use Setono\SyliusFeedPlugin\Generator\FeedPathGeneratorInterface;
use Setono\SyliusFeedPlugin\Generator\TemporaryFeedPathGenerator;
use Setono\SyliusFeedPlugin\Model\FeedInterface;
use Setono\SyliusFeedPlugin\Workflow\FeedGraph;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

final class MoveGeneratedFeedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly FilesystemOperator $temporaryFilesystem,
        private readonly FilesystemOperator $filesystem,
        private readonly FeedPathGeneratorInterface $temporaryFeedPathGenerator,
        private readonly FeedPathGeneratorInterface $feedPathGenerator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        $event = sprintf('workflow.%s.transition.%s', FeedGraph::GRAPH, FeedGraph::TRANSITION_PROCESSED);

        return [
            $event => 'move',
        ];
    }

    public function move(TransitionEvent $event): void
    {
        $feed = $event->getSubject();

        if (!$feed instanceof FeedInterface) {
            return;
        }

        /** @var ChannelInterface $channel */
        foreach ($feed->getChannels() as $channel) {
            foreach ($channel->getLocales() as $locale) {
                $temporaryDir = $this->temporaryFeedPathGenerator->generate(
                    $feed,
                    (string) $channel->getCode(),
                    (string) $locale->getCode(),
                );
                $temporaryFilesystem = $this->temporaryFilesystem;
                $temporaryPath = TemporaryFeedPathGenerator::getBaseFile($temporaryDir);
                $tempFile = $temporaryFilesystem->readStream((string) $temporaryPath);
                if (!\is_resource($tempFile)) {
                    throw new \RuntimeException(sprintf(
                        'The file with path "%s" could not be found',
                        $temporaryPath,
                    ));
                }

                // move the file from the temporary location to a temp file in the *not* temporary directory
                $newPath = $this->feedPathGenerator->generate(
                    $feed,
                    (string) $channel->getCode(),
                    (string) $locale->getCode(),
                );
                $path = sprintf('%s/%s', $newPath->getPath(), uniqid('feed-', true));

                $this->filesystem->writeStream($path, $tempFile);

                try {
                    $this->filesystem->delete((string) $newPath);
                } catch (UnableToDeleteFile) {
                }

                $this->filesystem->move($path, (string) $newPath);

                $temporaryFilesystem->delete((string) $temporaryPath);
            }
        }
    }
}
