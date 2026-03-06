<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Controller\Action\Shop;

use League\Flysystem\FilesystemOperator;
use RuntimeException;
use Setono\SyliusFeedPlugin\Generator\FeedPathGeneratorInterface;
use Setono\SyliusFeedPlugin\Repository\FeedRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mime\MimeTypesInterface;

final readonly class ShowFeedAction
{
    public function __construct(
        private FeedRepositoryInterface $repository,
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
        private FeedPathGeneratorInterface $feedPathGenerator,
        private FilesystemOperator $filesystem,
        private MimeTypesInterface $mimeTypes,
    ) {
    }

    public function __invoke(string $code): StreamedResponse
    {
        $feed = $this->repository->findOneByCode($code);
        if (null === $feed) {
            throw new NotFoundHttpException(sprintf('The feed with id %s does not exist', $code));
        }

        $channelCode = (string) $this->channelContext->getChannel()->getCode();
        $localeCode = $this->localeContext->getLocaleCode();

        $feedPath = $this->feedPathGenerator->generate($feed, $channelCode, $localeCode);

        if (!$this->filesystem->fileExists((string) $feedPath)) {
            throw new NotFoundHttpException(sprintf('The feed with id %s has not been generated', $code));
        }

        $stream = $this->filesystem->readStream((string) $feedPath);
        if (!\is_resource($stream)) {
            throw new RuntimeException(sprintf('An error occurred trying to read the feed file %s', $feedPath));
        }

        $contentType = $this->mimeTypes->getMimeTypes($feedPath->getExtension())[0];

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $contentType);
        $response->setCallback(static function () use ($stream): void {
            while (!feof($stream)) {
                echo fread($stream, 8192);

                flush();
            }

            flush();

            fclose($stream);
        });

        return $response;
    }
}
