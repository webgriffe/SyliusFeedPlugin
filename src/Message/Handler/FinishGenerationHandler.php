<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Message\Handler;

use Doctrine\Persistence\ObjectManager;
use InvalidArgumentException;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Setono\SyliusFeedPlugin\FeedType\FeedTypeInterface;
use Setono\SyliusFeedPlugin\Generator\FeedPathGeneratorInterface;
use Setono\SyliusFeedPlugin\Generator\TemporaryFeedPathGenerator;
use Setono\SyliusFeedPlugin\Message\Command\FinishGeneration;
use Setono\SyliusFeedPlugin\Model\FeedInterface;
use Setono\SyliusFeedPlugin\Registry\FeedTypeRegistryInterface;
use Setono\SyliusFeedPlugin\Repository\FeedRepositoryInterface;
use Setono\SyliusFeedPlugin\Workflow\FeedGraph;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Workflow\Registry;
use Throwable;
use Twig\Environment;
use Webmozart\Assert\Assert;

final class FinishGenerationHandler
{
    use GetFeedTrait;

    /**
     * @param FeedRepositoryInterface<FeedInterface> $feedRepository
     */
    public function __construct(
        FeedRepositoryInterface $feedRepository,
        private readonly ObjectManager $feedManager,
        private readonly FilesystemOperator $filesystem,
        private readonly Registry $workflowRegistry,
        private readonly Environment $twig,
        private readonly FeedTypeRegistryInterface $feedTypeRegistry,
        private readonly FeedPathGeneratorInterface $temporaryFeedPathGenerator,
        private readonly LoggerInterface $logger,
    ) {
        $this->feedRepository = $feedRepository;
    }

    public function __invoke(FinishGeneration $message): void
    {
        $feed = $this->getFeed($message->getFeedId());

        try {
            $workflow = $this->workflowRegistry->get($feed, FeedGraph::GRAPH);
        } catch (InvalidArgumentException $e) {
            throw new UnrecoverableMessageHandlingException(
                'An error occurred when trying to get the workflow for the feed',
                0,
                $e,
            );
        }

        try {
            $feedType = $this->feedTypeRegistry->get((string) $feed->getFeedType());

            /** @var ChannelInterface $channel */
            foreach ($feed->getChannels() as $channel) {
                foreach ($channel->getLocales() as $locale) {
                    $dir = $this->temporaryFeedPathGenerator->generate($feed, (string) $channel->getCode(), (string) $locale->getCode());

                    $batchStream = $this->getBatchStream();

                    [$feedStart, $feedEnd] = $this->getFeedParts($feed, $feedType, $channel, $locale);

                    fwrite($batchStream, $feedStart);

                    $filesystem = $this->filesystem;

                    /** @var array<array-key, array{basename: string, path: string}>|DirectoryListing<StorageAttributes> $files */
                    $files = $filesystem->listContents((string) $dir);
                    foreach ($files as $file) {
                        if (is_array($file)) {
                            Assert::keyExists($file, 'basename');
                            Assert::keyExists($file, 'path');

                            if (TemporaryFeedPathGenerator::BASE_FILENAME === $file['basename']) {
                                continue;
                            }
                        }
                        /** @var string $path */
                        $path = $file['path'];
                        $fp = $filesystem->readStream($path);
                        if (!is_resource($fp)) {
                            throw new \RuntimeException(sprintf(
                                'The file "%s" could not be opened as a resource',
                                $path,
                            ));
                        }

                        while (!feof($fp)) {
                            fwrite($batchStream, (string) fread($fp, 8192));
                        }

                        fclose($fp);

                        $filesystem->delete($path);
                    }

                    fwrite($batchStream, $feedEnd);

                    $filesystem->writeStream((string) TemporaryFeedPathGenerator::getBaseFile($dir), $batchStream);

                    // tries to close the file pointer although it may already have been closed by flysystem
                    fclose($batchStream);
                }
            }

            if (!$workflow->can($feed, FeedGraph::TRANSITION_PROCESSED)) {
                throw new RuntimeException(sprintf(
                    'The feed with id: %d can not be marked as ready because the feed is in a wrong state (%s)',
                    (int) $feed->getId(),
                    $feed->getState(),
                ));
            }

            $workflow->apply($feed, FeedGraph::TRANSITION_PROCESSED);

            $this->feedManager->flush();
        } catch (Throwable $e) {
            $this->logger->critical($e->getMessage(), ['feedId' => $feed->getId()]);

            if ($workflow->can($feed, FeedGraph::TRANSITION_ERRORED)) {
                $workflow->apply($feed, FeedGraph::TRANSITION_ERRORED);
                $this->feedManager->flush();
            }

            throw $e;
        }
    }

    /**
     * @return resource
     */
    private function getBatchStream()
    {
        // needs to be w+ since we use the same stream later to read from
        $resource = fopen('php://temp', 'w+b');

        if (!is_resource($resource)) {
            throw new RuntimeException('Could not open the stream');
        }

        return $resource;
    }

    /**
     * @return non-empty-list<string>
     */
    private function getFeedParts(
        FeedInterface $feed,
        FeedTypeInterface $feedType,
        ChannelInterface $channel,
        LocaleInterface $locale,
    ): array {
        $template = $this->twig->load('@SetonoSyliusFeedPlugin/feed/feed.txt.twig');

        $content = $template->render(
            array_merge(
                $feedType->getFeedContext()->getContext($feed, $channel, $locale),
                ['feed' => $feedType->getTemplate()],
            ),
        );

        return explode('<!-- ITEM_BOUNDARY -->', $content);
    }
}
