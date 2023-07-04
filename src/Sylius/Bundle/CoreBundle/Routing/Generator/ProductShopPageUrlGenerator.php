<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Routing\Generator;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductShopPageUrlGenerator implements ProductShopPageUrlGeneratorInterface
{
    public function __construct(
        private LocaleContextInterface $localeContext,
        private UrlGeneratorInterface $urlGenerator,
        private bool $unsecuredUrls,
    ) {
    }

    public function generate(ProductInterface $product, ChannelInterface $channel): ?string
    {
        /** @var Collection<array-key, ProductTranslationInterface> $productTranslations */
        $productTranslations = $product->getTranslations();

        $administratorLocaleCode = $this->localeContext->getLocaleCode();
        $productTranslation = $this->findTranslationWithSlugForLocales($productTranslations, [$administratorLocaleCode]);

        if (false !== $productTranslation) {
            return $this->generateProductUrl($productTranslation, $channel);
        }

        /** @var string $channelDefaultLocaleCode */
        $channelDefaultLocaleCode = $channel->getDefaultLocale()->getCode();
        $productTranslation = $this->findTranslationWithSlugForLocales($productTranslations, [$channelDefaultLocaleCode]);

        if (false !== $productTranslation) {
            return $this->generateProductUrl($productTranslation, $channel);
        }

        $localesEnabledInChannel = $this->getLocalesCodesEnabledInChannel($channel);
        $productTranslation = $this->findTranslationWithSlugForLocales($productTranslations, $localesEnabledInChannel);

        if (false !== $productTranslation) {
            return $this->generateProductUrl($productTranslation, $channel);
        }

        return null;
    }

    /**
     * @param Collection<array-key, ProductTranslationInterface> $productTranslations
     * @param array<string> $localeCodes
     */
    private function findTranslationWithSlugForLocales(Collection $productTranslations, array $localeCodes): ProductTranslationInterface|false
    {
        foreach ($productTranslations as $productTranslation) {
            $isLocaleCodeMatching = in_array($productTranslation->getLocale(), $localeCodes);
            $isSlugPresent = '' !== $productTranslation->getSlug() && null !== $productTranslation->getSlug();

            if ($isLocaleCodeMatching && $isSlugPresent) {
                return $productTranslation;
            }
        }

        return false;
    }

    private function generateProductUrl(ProductTranslationInterface $productTranslation, ChannelInterface $channel): string
    {
        $productPath = $this->urlGenerator->generate('sylius_shop_product_show', [
            'slug' => $productTranslation->getSlug(),
            '_locale' => $productTranslation->getLocale(),
        ]);

        if ($channel->getHostname() === null) {
            return $productPath;
        }

        return sprintf(
            '%s://%s%s',
            $this->unsecuredUrls ? 'http' : 'https',
            $channel->getHostname(),
            $productPath,
        );
    }

    /** @return array<array-key, string> */
    private function getLocalesCodesEnabledInChannel(ChannelInterface $channel): array
    {
        return $channel->getLocales()->map(function (LocaleInterface $locale): string {
            return $locale->getCode();
        })->toArray();
    }
}
