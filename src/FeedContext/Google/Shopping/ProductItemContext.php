<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\FeedContext\Google\Shopping;

use InvalidArgumentException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Setono\SyliusFeedPlugin\Feed\Model\Google\Shopping\Availability;
use Setono\SyliusFeedPlugin\Feed\Model\Google\Shopping\Condition;
use Setono\SyliusFeedPlugin\Feed\Model\Google\Shopping\Price;
use Setono\SyliusFeedPlugin\Feed\Model\Google\Shopping\Product;
use Setono\SyliusFeedPlugin\FeedContext\ContextList;
use Setono\SyliusFeedPlugin\FeedContext\ContextListInterface;
use Setono\SyliusFeedPlugin\FeedContext\ItemContextInterface;
use Setono\SyliusFeedPlugin\Model\ConditionAwareInterface;
use Setono\SyliusFeedPlugin\Model\FeedInterface;
use Setono\SyliusFeedPlugin\Model\GtinAwareInterface;
use Setono\SyliusFeedPlugin\Model\LocalizedBrandAwareInterface;
use Setono\SyliusFeedPlugin\Model\LocalizedColorAwareInterface;
use Setono\SyliusFeedPlugin\Model\LocalizedSizeAwareInterface;
use Setono\SyliusFeedPlugin\Model\MpnAwareInterface;
use Setono\SyliusFeedPlugin\Model\TaxonPathAwareInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Model\ProductImagesAwareInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Sylius\Resource\Model\TranslatableInterface;
use Sylius\Resource\Model\TranslationInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final readonly class ProductItemContext implements ItemContextInterface
{
    public function __construct(
        private RouterInterface $router,
        private CacheManager $cacheManager,
        private AvailabilityCheckerInterface $availabilityChecker,
        private bool $excludeRootTaxon,
    ) {
    }

    #[\Override]
    public function getContextList(
        object $object,
        ChannelInterface $channel,
        LocaleInterface $locale,
        FeedInterface $feed,
    ): ContextListInterface {
        $product = $object;
        if (!$product instanceof ProductInterface) {
            throw new InvalidArgumentException(sprintf(
                'The class %s is not an instance of %s',
                $product::class,
                ProductInterface::class,
            ));
        }

        if ($product instanceof TaxonPathAwareInterface) {
            $productType = $product->getTaxonPath($locale, $this->excludeRootTaxon);
        } else {
            $productType = $this->getProductType($product, $locale, $this->excludeRootTaxon);
        }
        Assert::isInstanceOf($product, TranslatableInterface::class);

        /** @var ProductTranslationInterface|null $translation */
        $translation = $this->getTranslation($product, (string) $locale->getCode());
        $contextList = new ContextList();
        foreach ($product->getVariants() as $variant) {
            Assert::isInstanceOf($variant, ProductVariantInterface::class);
            $data = new Product();
            $data->setId($variant->getCode());
            $data->setItemGroupId($product->getCode());
            $data->setImageLink($this->getVariantImageLink($variant) ?? $this->getImageLink($product));
            $data->setAvailability($this->getAvailability($variant));

            [$price, $salePrice] = $this->getPrices($variant, $channel);
            $data->setPrice($price);
            $data->setSalePrice($salePrice);

            if (null !== $translation) {
                $data->setTitle($translation->getName());
                $data->setDescription($translation->getDescription());
                $data->setLink($this->getLink($locale, $translation));
            }

            $data->setCondition(
                $product instanceof ConditionAwareInterface && ($condition = $product->getCondition()) !== null ?
                    Condition::from((string) $condition) : Condition::new,
            );

            if (null !== $productType) {
                $data->setProductType($productType);
            }

            if ($variant instanceof LocalizedBrandAwareInterface && $variant->getBrand($locale) !== null) {
                $data->setBrand((string) $variant->getBrand($locale));
            } elseif ($product instanceof LocalizedBrandAwareInterface && $product->getBrand($locale) !== null) {
                $data->setBrand((string) $product->getBrand($locale));
            }

            if ($variant instanceof GtinAwareInterface && $variant->getGtin() !== null) {
                $data->setGtin((string) $variant->getGtin());
            } elseif ($product instanceof GtinAwareInterface && $product->getGtin() !== null) {
                $data->setGtin((string) $product->getGtin());
            }

            if ($variant instanceof MpnAwareInterface && $variant->getMpn() !== null) {
                $data->setMpn((string) $variant->getMpn());
            } elseif ($product instanceof MpnAwareInterface && $product->getMpn() !== null) {
                $data->setMpn((string) $product->getMpn());
            }

            if ($variant instanceof LocalizedSizeAwareInterface && $variant->getSize($locale) !== null) {
                $data->setSize((string) $variant->getSize($locale));
            } elseif ($product instanceof LocalizedSizeAwareInterface && $product->getSize($locale) !== null) {
                $data->setSize((string) $product->getSize($locale));
            }

            if ($variant instanceof LocalizedColorAwareInterface && $variant->getColor($locale) !== null) {
                $data->setColor((string) $variant->getColor($locale));
            } elseif ($product instanceof LocalizedColorAwareInterface && $product->getColor($locale) !== null) {
                $data->setColor((string) $product->getColor($locale));
            }

            $contextList->add($data);
        }

        return $contextList;
    }

    private function getTranslation(TranslatableInterface $translatable, string $locale): ?TranslationInterface
    {
        foreach ($translatable->getTranslations() as $translation) {
            if ($translation->getLocale() === $locale) {
                return $translation;
            }
        }

        return null;
    }

    private function getLink(LocaleInterface $locale, ProductTranslationInterface $translation): ?string
    {
        if ($translation->getSlug() === null || $locale->getCode() === null) {
            return null;
        }

        return $this->router->generate(
            'sylius_shop_product_show',
            ['slug' => $translation->getSlug(), '_locale' => $locale->getCode()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }

    private function getVariantImageLink(ProductImagesAwareInterface $imagesAware): ?string
    {
        $images = $imagesAware->getImagesByType('main');
        if ($images->count() === 0) {
            $images = $imagesAware->getImages();
        }
        if ($images->count() === 0) {
            return null;
        }

        /** @var ImageInterface|false $image */
        $image = $images->first();
        if (false === $image) {
            return null;
        }

        return $this->cacheManager->getBrowserPath((string) $image->getPath(), 'sylius_shop_product_large_thumbnail');
    }

    private function getImageLink(ImagesAwareInterface $imagesAware): ?string
    {
        $images = $imagesAware->getImagesByType('main');
        if ($images->count() === 0) {
            $images = $imagesAware->getImages();
        }

        if ($images->count() === 0) {
            return null;
        }

        /** @var ImageInterface|false $image */
        $image = $images->first();
        if (false === $image) {
            return null;
        }

        return $this->cacheManager->getBrowserPath((string) $image->getPath(), 'sylius_shop_product_large_thumbnail');
    }

    /**
     * Index 0 equals the price
     * Index 1 equals the sale price (if set)
     */
    private function getPrices(ProductVariantInterface $variant, ChannelInterface $channel): array
    {
        $channelPricing = $variant->getChannelPricingForChannel($channel);

        if (null === $channelPricing) {
            return [null, null];
        }

        $originalPrice = $channelPricing->getOriginalPrice();
        $price = $channelPricing->getPrice();

        if (null === $price) {
            return [null, null];
        }

        if (null === $originalPrice) {
            return [$this->createPrice($price, $channel), null];
        }

        return [$this->createPrice($originalPrice, $channel), $this->createPrice($price, $channel)];
    }

    private function getAvailability(ProductVariantInterface $product): Availability
    {
        return $this->availabilityChecker->isStockAvailable($product) ? Availability::inStock : Availability::outOfStock;
    }

    private function createPrice(int $price, ChannelInterface $channel): ?Price
    {
        $baseCurrency = $channel->getBaseCurrency();
        if (null === $baseCurrency) {
            return null;
        }

        return new Price($price, (string) $baseCurrency->getCode());
    }

    private function getProductType(ProductInterface $product, LocaleInterface $locale, bool $excludeRoot = false): ?string
    {
        if ($product->getMainTaxon() !== null) {
            $taxon = $product->getMainTaxon();
        } elseif (count($product->getTaxons()) > 0) {
            $taxon = $product->getTaxons()->first();
        } else {
            return null;
        }
        Assert::isInstanceOf($taxon, TaxonInterface::class);

        $breadcrumbs = [];
        array_unshift($breadcrumbs, $taxon);
        for ($breadcrumb = $taxon->getParent(); $breadcrumb instanceof TaxonInterface; $breadcrumb = $breadcrumb->getParent()) {
            array_unshift($breadcrumbs, $breadcrumb);
        }

        if ($excludeRoot) {
            // In cases when some root taxon assigned to channel's menuTaxon,
            // we don't want to display root taxon - remove first item
            array_shift($breadcrumbs);
        }

        return implode(' > ', array_map(function (TaxonInterface $breadcrumb) use ($locale): string {
            $translation = $this->getTranslation($breadcrumb, (string) $locale->getCode());

            // Fallback to default locale
            return $translation instanceof TaxonTranslationInterface ? (string) $translation->getName() : (string) $breadcrumb->getName();
        }, $breadcrumbs));
    }
}
