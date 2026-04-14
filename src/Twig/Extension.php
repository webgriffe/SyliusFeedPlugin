<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Twig;

use Setono\SyliusFeedPlugin\Model\FeedInterface;
use Setono\SyliusFeedPlugin\Repository\FeedRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class Extension extends AbstractExtension
{
    /**
     * @param FeedRepositoryInterface<FeedInterface> $feedRepository
     */
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly FeedRepositoryInterface $feedRepository,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('setono_sylius_feed_remove_empty_tags', $this->removeEmptyTags(...)),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('setono_sylius_feed_generate_feed_url', $this->generateFeedUrl(...)),
            new TwigFunction('setono_sylius_feed_find_feed', $this->findFeed(...)),
        ];
    }

    public function findFeed(int $id): ?FeedInterface
    {
        return $this->feedRepository->find($id);
    }

    public function removeEmptyTags(string $xml): string
    {
        // Remove empty CDATA sections before checking for empty tags
        $xml = (string) preg_replace('#<!\[CDATA\[\s*\]\]>#', '', $xml);

        // Exclude CDATA from matching as "opening tags" by requiring the tag not to start with "!"
        return (string) preg_replace('#<[^/>!][^>]*></[^>]+>#', '', $xml);
    }

    public function generateFeedUrl(FeedInterface $feed, ChannelInterface $channel, LocaleInterface $locale): string
    {
        $path = $this->urlGenerator->generate('setono_sylius_feed_shop_feed_show', [
            'code' => $feed->getCode(),
        ]);

        // todo maybe inject request context into router instead to 'make it right'
        return sprintf('%s://%s%s', $this->getScheme(), (string) $channel->getHostname(), $path);
    }

    private function getScheme(): string
    {
        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            return 'https';
        }

        return $request->getScheme();
    }
}
